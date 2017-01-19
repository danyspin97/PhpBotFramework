<?php

namespace PhpBotFramework\Utilities;

trait Localization {

    /**
     * \addtogroup Multilanguage Multilanguage
     * \brief Methods to create a localized bot.
     * @{
     */

    /** \brief Store the language for a multi-language bot */
    public $language;

    /** \brief Store localization data */
    public $local;

    /** \brief Table contaning bot users data in the sql database. */
    public $user_table = '"User"';

    /** \brief Name of the column that represents the user id in the sql database */
    public $id_column = 'chat_id';

    /** @} */

    /**
     * \brief Get current user language from the database, and set it in $language.
     * @param $default_language <i>Optional</i>. Default language to return in case of errors.
     * @return Language set for the current user, $default_language on errors.
     */
    public function getLanguageDatabase($default_language = 'en') {

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
    public function getLanguageRedis($default_language = 'en', $expiring_time = '86400') : string {

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
    public function setLanguageRedis($language, $expiring_time = '86400') {

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

    /**
     * \brief Load localization files (JSON-serialized) from a folder and set them in $local variable.
     * \details Save all localization files, saved as json format, from a directory and put the contents in $local variable.
     * Each file will be saved into $local with the first two letters of the filename as the index.
     * Access the english data as $this->local["en"]["Your key"].
     * File <code>./localization/en.json</code>:
     *
     *     {"Hello_Msg": "Hello"}
     *
     * File <code>./localization/it.json</code>:
     *
     *     {"Hello_Msg": "Ciao"}
     *
     * Usage in <code>processMessage()</code>:
     *
     *     $sendMessage($this->local[$this->language]["Hello_Msg"]);
     *
     * @param $dir Directory where the localization files are saved.
     */
    public function loadLocalization($dir = './localization') {

        // Open directory
        if ($handle = opendir($dir)) {

            // Iterate over all files
            while (false !== ($file = readdir($handle))) {

                // If the file is a JSON data file
                if (strlen($file) > 6 && substr($file, -5) === '.json') {

                    try {

                        // Add the contents of the file to the $local variable, after deserializng it from JSON format
                        // The contents will be added with the 2 letter of the file as the index
                        $this->local[substr($file, 0, 2)] = json_decode(file_get_contents("$dir/$file"), true);

                    } catch (BotException $e) {

                        echo $e->getMessage();

                    }

                }

            }

        }

    }

    /** @} */
}
