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

    /** \brief Store the language for a multi-language bot */
    public $language;

    /** Pdo connection to the database. */
    public $pdo;

    /** \brief Table contaning bot users data in the sql database. */
    public $user_table = '"User"';

    /** \brief Name of the column that represents the user id in the sql database */
    public $id_column = 'chat_id';

    /**
     * \brief Get current user language from the database, and set it in $language.
     * @param $default_language <i>Optional</i>. Default language to return in case of errors.
     * @return Language set for the current user, $default_language on errors.
     */
    public function getLanguageDatabase($default_language = 'en')
    {

        // If we have no database
        if (!isset($this->_database)) {
            // Set the language to english
            $this->language = $default_language;

            // Return english
            return $default_language;
        }

        // Get the language from the bot
        $sth = $this->pdo->prepare('SELECT language FROM ' . $this->user_table . ' WHERE ' . $this->id_column . ' = :chat_id');
        $sth->bindParam(':chat_id', $this->_chat_id);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $row = $sth->fetch();

        $sth = null;

        // If we got the language
        if (isset($row['language'])) {
            // Set the language in the bot
            $this->language = $row['language'];

            // And return it
            return $row['language'];
        }

        // If we couldn't get it, set the language to english
        $this->language = $default_language;

        // and return english
        return $this->language;
    }

    /**
     * \brief Get current user language from redis, as a cache, and set it in language.
     * \details Using redis database as cache, seeks the language in it, if there isn't
     * then get the language from the sql database and store it (with default expiring of one day) in redis.
     * It also change $language parameter of the bot to the language returned.
     * @param $default_language <i>Optional</i>. Default language to return in case of errors.
     * @param $expiring_time <i>Optional</i>. Set the expiring time for the language on redis each time it is took from the sql database.
     * @return Language for the current user, $default_language on errors.
     */
    public function getLanguageRedis($default_language = 'en', $expiring_time = '86400') : string
    {

        // If redis or pdo connection are not set
        if (!isset($this->redis) || !isset($this->pdo)) {
            // return default language
            return $default_language;
        }

        // Does it exists on redis?
        if ($this->redis->exists($this->_chat_id . ':language')) {
            // Get the value
            $this->language = $this->redis->get($this->_chat_id . ':language');
            return $this->language;
        }

        // Set the value from the db
        $this->redis->setEx($this->_chat_id . ':language', $expiring_time, $this->getLanguageDatabase($default_language));

        // and return it
        return $this->language;
    }

    /**
     * \brief Set the current user language in both redis, sql database and $language.
     * \details Save it on database first, then create the expiring key on redis.
     * @param $language The new language to set.
     * @param $expiring_time <i>Optional</i>. Time for the language key in redis to expire.
     * @return On sucess, return true, throw exception otherwise.
     */
    public function setLanguageRedis($language, $expiring_time = '86400')
    {

        // Check database connection
        if (!isset($this->_database) && !isset($this->redis)) {
            throw new BotException('Database connection not set');
        }

        // Update the language in the database
        $sth = $this->pdo->prepare('UPDATE ' . $this->user_table . ' SET language = :language WHERE ' . $this->id_column . ' = :id');
        $sth->bindParam(':language', $language);
        $sth->bindParam(':id', $this->_chat_id);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            throw new BotException($e->getMessage());
        }

        // Destroy statement
        $sth = null;

        // Set the language in redis with expiring
        $this->redis->setEx($this->_chat_id . ':language', $expiring_time, $language);

        // Set language in the bot variable
        $this->language = $language;
    }

    /** @} */
}
