<?php

/*
 * This file is part of the PhpBotFramework.
 *
 * PhpBotFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * PhpBotFramework is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhpBotFramework\Localization;

trait Language
{

    /**
     * \addtogroup Localization Localization
     * @{
     */

    /** \brief Stores the language for a multi-language bot */
    public $language;

    /** PDO connection to the database. */
    public $pdo;

    /** \brief Table containing bot users data into database. */
    public $user_table = '"User"';

    /** \brief Name of the column that represents the user ID into database */
    public $id_column = 'chat_id';

    /**
     * \brief Get current user's language from the database, and set it in $language.
     * @param $default_language <i>Optional</i>. Default language to return in case of errors.
     * @return Language set for the current user, $default_language on errors.
     */
    public function getLanguageDatabase($default_language = 'en')
    {
        // If we have no database
        if (!isset($this->_database)) {
            $this->language = $default_language;
            return $default_language;
        }

        // Get the language from the bot
        $sth = $this->pdo->prepare('SELECT language FROM ' . $this->user_table . ' WHERE '
                                                           . $this->id_column . ' = :chat_id');
        $sth->bindParam(':chat_id', $this->_chat_id);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $row = $sth->fetch();
        $sth = null;

        if (isset($row['language'])) {
            $this->language = $row['language'];
            return $row['language'];
        }

        // If we couldn't get it, set the language to the default one
        $this->language = $default_language;
        return $this->language;
    }

    /**
     * \brief Get current user language from Redis (as a cache) and set it in language.
     * \details Using Redis as cache, check for the language. On failure, get the language
     * from the database and store it (with default expiring time of one day) in Redis.
     *
     * It also change $language parameter of the bot to the language returned.
     * @param $default_language <i>Optional</i>. Default language to return in case of errors.
     * @param $expiring_time <i>Optional</i>. Set the expiring time for the language on
     * redis each time it is took from the sql database.
     * @return Language for the current user, $default_language on errors.
     */
    public function getLanguageRedis($default_language = 'en', $expiring_time = '86400') : string
    {
        if (!isset($this->redis) || !isset($this->pdo)) {
            return $default_language;
        }

        // Check if the language exists on Redis
        if ($this->redis->exists($this->_chat_id . ':language')) {
            $this->language = $this->redis->get($this->_chat_id . ':language');
            return $this->language;
        }

        // Set the value from the database
        $this->redis->setEx(
            $this->_chat_id . ':language',
            $expiring_time,
            $this->getLanguageDatabase($default_language)
        );
        return $this->language;
    }

    /**
     * \brief Set the current user language in both Redis, database and internally.
     * \details Save it into database first, then create the expiring key on Redis.
     * @param $language The language to set.
     * @param $expiring_time <i>Optional</i>. Time for the language key in redis to expire.
     * @return On sucess, return true, throws exception otherwise.
     */
    public function setLanguageRedis($language, $expiring_time = '86400')
    {
        if (!isset($this->_database) && !isset($this->redis)) {
            throw new BotException('Database connection not set');
        }

        // Update the language in the database
        $sth = $this->pdo->prepare('UPDATE ' . $this->user_table . ' SET language = :language WHERE '
                                             . $this->id_column . ' = :id');
        $sth->bindParam(':language', $language);
        $sth->bindParam(':id', $this->_chat_id);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw new BotException($e->getMessage());
        }

        $sth = null;

        // Set the language in Redis
        $this->redis->setEx($this->_chat_id . ':language', $expiring_time, $language);

        // Set language internally
        $this->language = $language;
    }

    /** @} */
}
