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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GN
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhpBotFramework\Localization;

use PhpBotFramework\Exceptions\BotException;

trait Language
{

    /**
     * \addtogroup Localization Localization
     * \brief Create a localized bot.
     * \details Using both a sql database and a redis database you can develop a localized bot with just a small impact on the performance.
     * By default the sql database will store the language permanently in a table which name is defined in $user_table.
     * The redis database will cache the language for the chat_id for a variable time.
     * You can use only the sql database if a redis database is not accessible.
     * These methods will treat groups as User so they will be stored in the table as normal user does.
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
     * @param string $default_language <i>Optional</i>. Default language to return in case of errors.
     * @return string Language set for the current user, $default_language on errors.
     */
    public function getLanguageDatabase(string $default_language = 'en')
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
     * \brief Set the current user language in database and internally.
     * \details Save it into database first.
     * @param string $language The language to set.
     * @return bool On sucess, return true, throws exception otherwise.
     */
    public function setLanguageDatabase(string $language) : bool
    {
        if (!isset($this->_database)) {
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

        // Set language internally
        $this->language = $language;

        return true;
    }

    /**
     * \brief Get current user language from Redis (as a cache) and set it in language.
     * \details Using Redis as cache, check for the language. On failure, get the language
     * from the database and store it (with default expiring time of one day) in Redis.
     *
     * It also change $language parameter of the bot to the language returned.
     * @param string $default_language <i>Optional</i>. Default language to return in case of errors.
     * @param int $expiring_time <i>Optional</i>. Set the expiring time for the language on
     * redis each time it is took from the sql database.
     * @return string Language for the current user, $default_language on errors.
     */
    public function getLanguageRedis(string $default_language = 'en', int $expiring_time = 86400) : string
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
     * @param string $language The language to set.
     * @param int $expiring_time <i>Optional</i>. Time for the language key in redis to expire.
     * @return bool On sucess, return true, throws exception otherwise.
     */
    public function setLanguageRedis(string $language, int $expiring_time = 86400) : bool
    {
        if (!isset($this->redis)) {
            throw new BotException('Database connection not set');
        }

        // If we could successfully set the language in the database
        if ($this->setLanguageDatabase($language)) {
            // Set the language in Redis
            $this->redis->setEx($this->_chat_id . ':language', $expiring_time, $language);
            return true;
        }

        return false;
    }

    /** @} */
}
