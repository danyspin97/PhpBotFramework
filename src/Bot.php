<?php

namespace DanySpin97\PhpBotFramework;

/**
 * \class Bot Bot
 * \brief Bot class to handle updates and commandes.
 * \details Class Bot to handle task like api request, or more specific api function(sendMessage, editMessageText, etc).
 * Usage example in webhook.php
 *
 */
class Bot extends CoreBot {

    /**
     * \addtogroup Core Core(internal)
     * @{
     */

    /** \brief Store the command handled by the bot */
    private $message_commands;

    /** \brief Does the bot has command set? Set by initBot */
    private $message_set;

    /** @} */

    /**
     * \modules Bot Bot
     * @{
     */
    /** \brief Text received in messages */
    private $text;

    /** \brief Data received in callback query */
    private $data;

    /** \brief Query sent by the user in the inline query */
    private $query;

    /** \brief Store the inline keyboard */
    public $inline_keyboard;

    /** \brief Pdo reference */
    public $pdo;

    /** \brief Redis connection */
    public $redis;

    /** @} */

    /**
     * \addtogroup Multilanguage Multilanguage
     * @{
     */

    /** \brief Store the language for a multi-language bot */
    public $language;

    /** \brief Store the array containing language */
    public $localization;

    /** \brief Table contaning bot users data in the sql database. */
    public $user_table = '"User"';

    /** \brief Name of the column that represents the user id in the sql database */
    public $id_column = 'chat_id';

    /** @} */

    /** \addtogroup State
     * @{
     */

    /** \brief Status of the bot to handle data inserting and menu-like bot. */
    public $status;

    /** @} */

    /**
     * \addtogroup Bot
     * @{
     */

    /**
     * \brief Construct an empy bot.
     * \details Construct a bot with commands, multilanguage and status.
     */
    public function __construct(string $token) {

        // Parent constructor
        parent::__construct($token);

        // Initialize to an empty array
        $message_commands = [];

    }

    /** \brief Descruct the class. */
    public function __destruct() {

        parent::__destruct();

        // Close redis connection if it is open
        if (isset($this->redis)) {

            $this->redis->close();

        }

    }

    /**
     * \brief Get the text of the message (for updates of type "message").
     * @return The text of the current message, throw exception if the current update is not a message.
     */
    public function &getText() {

        if (isset($this->text)) {

            return $this->text;

        }

        throw new BotException("Text not set: wrong update type");

    }

    /**
     * \brief Get the data received from the callback query (for updates of type "callback_query").
     * @return The data of the current callback, throw exception if the current update is not a callback query.
     */
    public function &getData() {

        if (isset($this->data)) {

            return $this->data;

        }

        throw new BotException("Data not set: wrong update type");

    }

    /**
     * \brief Get the query received from the inline query (for updates of type "inline_query").
     * @return The query sent by the user, throw exception if the current update is not an inline query.
     */
    public function &getQuery() {

        if (isset($this->query)) {

            return $this->query;

        }

        throw new BotException("Query not set: wrong update type");
    }

    /**
     * \brief Get update and process it.
     * \details Call this method if you are using webhook.
     * It will get update from php::\input, check it and then process it using processUpdate.
     */
    public function processWebhookUpdate() {

        $this->initBot();

        $this->processUpdate(json_decode(file_get_contents('php://input'), true));

    }

    /** @} */

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    /**
     * \brief Set some internal Data to work optimized
     */
    private function initBot() {

        $this->message_set = !empty($this->message_commands);

    }

    /**
     * \brief Dispatch each update to the right method (processMessage, processCallbackQuery, etc).
     * \details Set $chat_id for each update, $text, $data and $query are set for each update that contains them.
     * It also calls commands for each updates, before process methods.
     * @param $update Reference to the update received.
     * @return The id of the update processed.
     */
    public function processUpdate(array &$update) : int {

        if (isset($update['message'])) {

            // Set data from the message
            $this->chat_id = $update['message']['from']['id'];
            $this->text = $update['message']['text'];

            if ($this->message_set && isset($update['message']['entities']) && $update['message']['entities'][0]['type'] === 'bot_command') {

                // The lenght of the command
                $length = $update['message']['entities'][0]['length'];

                // For each command added by the user
                foreach ($this->message_commands as $trigger) {

                    // Check corresponding
                    if ($trigger['length'] == $length && mb_strpos($trigger['command'], $this->text) !== false) {

                        // Execute script,
                        $trigger['script']($this, $update['message']);

                        // clear text variable
                        unset($this->text);

                        // and return the id of the current update
                        return $update['update_id'];

                    }

                }

            }

            // And process it
            $this->processMessage($update['message']);

            // Clear text
            unset($this->text);

        } elseif (isset($update['callback_query'])) {

            $this->chat_id = $update['callback_query']['from']['id'];
            $this->data = $update['callback_query']['data'];

            $this->processCallbackQuery($update['callback_query']);

            unset($this->data);

        } elseif (isset($update['inline_query'])) {

            $this->chat_id = $update['inline_query']['from']['id'];
            $this->query = $update['inline_query']['query'];

            $this->processInlineQuery($update['inline_query']);

            unset($this->query);

        } elseif (isset($update['edited_message'])) {

            $this->chat_id = $update['edited_message']['from']['id'];

            $this->processEditedMessage($update['edited_message']);

        } elseif (isset($update['chosen_inline_result'])) {

            $this->chat_id = $update['chosen_inline_result']['from']['id'];

            $this->processInlineResult($update['chosen_inline_result']);
        }

        return $update['update_id'];

    }

    /** @} */

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /**
     * \brief Called every message received by the bot.
     * \details Override it to script the bot answer for each message.
     * $chat_id and $text(use getText to access it) set inside of this function.
     * @param $message Reference to the message received.
     */
    protected function processMessage(&$message) {}

    /**
     * \brief Called every callback query received by the bot.
     * \details Override it to script the bot answer for each callback.
     * $chat_id and $data(use getData() to access it) set inside of this function.
     * @param $callback_query Reference to the callback query received.
     */
    protected function processCallbackQuery(&$callback_query) {}

    /**
     * \brief Called every inline query received by the bot.
     * \details Override it to script the bot answer for each inline query.
     * $chat_id and $query(use getQuery() to access it) set inside of this function.
     * @param $inline_query Reference to the inline query received.
     */
    protected function processInlineQuery(&$inline_query) {}

    /**
     * \brief Called every chosen inline result received by the bot.
     * \details Override it to script the bot answer for each chosen inline result.
     * $chat_id set inside of this function.
     * @param $chosen_inline_result Reference to the chosen inline result received.
     */
    protected function processChosenInlineResult(&$chosen_inline_result) {}

    /**
     * \brief Called every chosen edited message received by the bot.
     * \details Override it to script the bot answer for each edited message.
     * $chat_id set inside of this function.
     * @param $edited_message Reference to the edited message received.
     */
    protected function processEditedMessage(&$edited_message) {}

    /**
     * \brief Get updates received by the bot, using redis to save and get the last offset.
     * \details It check if an offset exists on redis, then get it, or call getUpdates to set it.
     * Then it start an infinite loop where it process updates and update the offset on redis.
     * Each update is surrounded by a try/catch.
     * @see getUpdates
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     * @param $offset_key <i>Optional</i>. Name of the variable where the offset is saved on Redis
     */
    public function getUpdatesRedis(int $limit = 100, int $timeout = 60, string $offset_key = 'offset') {

        // Check redis connection
        if (!isset($this->redis)) {

            throw new BotException("Redis connection is not set");

        }

        // If offset is already set in redis
        if ($this->redis->exists($variable_name)) {

            // just set $offset as the same value
            $offset = $this->redis->get($variable_name);

        } else {
        // Else get the offset from the id from the first update received

            $update = [];

            do {
                $update = $this->getUpdates(0, 1);
            } while (empty($update));

            $offset = $update[0]['update_id'];

            $this->redis->set($variable_name, $offset);

            $update = null;

        }

        $this->initBot();

        // Process all updates received
        while (true) {

            $updates = $this->getUpdates($offset, $limit, $timeout);

            // Parse all updates received
            foreach ($updates as $key => $update) {

                try {

                    $this->processUpdate($update);

                } catch (BotException $e) {

                    echo $e->getMessage();

                }

            }

            // Update the offset in redis
            $this->redis->set($variable_name, $offset + count($updates));
        }

    }

    /**
     * \brief Get updates received by the bot, and hold the offset in $offset.
     * \details Get the update_id of the first update to parse, set it in $offset and
     * then it start an infinite loop where it processes updates and keep $offset on the update_id of the last update received.
     * Each processUpdate() method call is surrounded by a try/catch.
     * @see getUpdates
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     */
    public function getUpdatesLocal(int $limit = 100, int $timeout = 60) {

        $update = [];

        // While there aren't updates to process
        do {

            // Get updates from telegram
            $update = $this->getUpdates(0, 1);

        // While in the array received there aren't updates
        } while (empty($update));

        // Set the offset to the first update recevied
        $offset = $update[0]['update_id'];

        $update = null;

        $this->initBot();

        // Process all updates
        while (true) {

            // Set parameter for the url call
            $parameters = [
                    'offset' => &$offset,
                    'limit' => &$limit,
                    'timeout' => &$timeout
            ];

            $updates = $this->exec_curl_request($this->api_url . 'getUpdates?' . http_build_query($parameters));

            // Parse all update to receive
            foreach ($updates as $key => $update) {

                try {

                    // Process one at a time
                    $this->processUpdate($update);

                } catch (BotException $e) {

                    echo $e->getMessage();

                }

            }

            // Update the offset
            $offset += sizeof($updates);

        }

    }

    /**
     * \brief Get updates received by the bot, using the sql database to store and get the last offset.
     * \details It check if an offset exists on redis, then get it, or call getUpdates to set it.
     * Then it start an infinite loop where it process updates and update the offset on redis.
     * Each update is surrounded by a try/catch.
     * @see getUpdates
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     * @param $table_name <i>Optional</i>. Name of the table where offset is saved in the database
     * @param $column_name <i>Optional</i>. Name of the column where the offset is saved in the database
     */
    public function getUpdatesDatabase(int $limit = 100, int $timeout = 0, string $table_name = 'telegram', string $column_name = 'bot_offset') {

        if (!isset($this->database)) {

            throw new BotException("Database connection is not set");

        }

        // Get the offset from the database
        $sth = $this->pdo->prepare('SELECT ' . $column_name . ' FROM ' . $table_name);

        try {

            $sth->execute();

        } catch (PDOException $e) {

            echo $e->getMessage();

        }

        $offset = $sth->fetchColumn();
        $sth = null;

        // Get the offset from the first update to update
        if ($offset === false) {

            $update = [];

            do {
                $update = $this->getUpdates(0, 1);
            } while (empty($update));

            $offset = $update[0]['update_id'];

            $update = null;

        }

        // Prepare the query for updating the offset in the database
        $sth = $this->pdo->prepare('UPDATE "' . $table_name . '" SET "' . $column_name . '" = :new_offset');

        $this->initBot();

        while (true) {

            $updates = $this->getUpdates($offset, $limit, $timeout);

            foreach ($updates as $key => $update) {

                try {

                    $this->processUpdate($update);

                } catch (BotException $e) {

                    echo $e->getMessage();

                }

            }

            // Update the offset on the database
            $sth->bindParam(':new_offset', $offset + sizeof($updates));
            $sth->execute();
        }
    }

    /**
     * \brief Add a function that will be executed everytime a message contain the selected command
     * \details Use this syntax:
     *
     *     addMessageCommand("start", function($bot, $message) {
     *         $bot->sendMessage("Hi"); });
     * @param $command The command that will trigger this function (without slash). Eg: "start", "help", "about"
     * @param $script The function that will be triggered by a command. Must take an object(the bot) and an array(the message received).
     */
    public function addMessageCommand(string $command, $script) {

        $this->message_commands[] = [
                'command' => '/' . $command,
                'script' => $script,
                'length' => mb_strlen($command) + 1
        ];

    }

    /** @} */

    /**
     * \addtogroup Optmized Optmized Api Methods
     * @{
     */

    /**
     * \brief Optimized version of sendMessage that takes reference of $text and reference of $inline_keyboard.
     * @see sendMessage
     */
    public function &sendMessageKeyboard(&$text, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_preview = true, $disable_notification = false) {

        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => $parse_mode,
            'reply_markup' => &$inline_keyboard,
            'disable_web_page_preview' => &$disable_web_preview,
            'disable_notification' => &$disable_notification
        ];

        return $this->exec_curl_request($this->api_url . 'sendMessage?' . http_build_query($parameters));

    }

    /**
     * \brief Optimized version of editMessageText that takes reference of $text and $inline_keyboard.
     * @see editMessageText
     */
    public function &editMessageTextKeyboard(&$text, &$inline_keyboard, &$message_id, $parse_mode = 'HTML', $disable_web_preview = false) {

        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'text' => &$text,
            'reply_markup' => &$inline_keyboard,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_preview,
        ];

        return $this->exec_curl_request($$this->api_url . 'editMessageText?' . http_build_query($parameters));

    }

    /**
     * \brief Optimized version of answerInlineQuery that takes reference of $results.
     * @see answerInlineQuery
     */
    public function &answerInlineQueryRef(&$results, $switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {

        $parameters = [
            'inline_query_id' => &$this->update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => &$switch_pm_parameter,
            'results' => &$results,
            'cache_time' => $cache_time
        ];

        return $this->exec_curl_request($this->api_url . 'answerInlineQuery?' . http_build_query($parameters));

    }

    /** @} */

    /**
     * \addtogroup Multilanguage Multilanguage
     * @{
     */

    /**
     * \brief Get current user language from the database, and set it in $language.
     * @param $default_language <i>Optional</i>. Default language to return in case of errors.
     * @return Language set for the current user, $default_language on errors.
     */
    public function &getLanguageDatabase($default_language = 'en') {

        // If we have no database
        if (!isset($this->database)) {

            // Set the language to english
            $this->language = $default_language;

            // Return english
            return $default_language;

        }

        // Get the language from the bot
        $sth = $this->pdo->prepare('SELECT language FROM ' . $this->user_table . ' WHERE ' . $this->id_column . ' = :chat_id');
        $sth->bindParam(':chat_id', $this->chat_id);

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
     * \brief Get current user language from redis, and set it in language.
     * \details Using redis database we get language stored and the value does not expires.
     * @param $default_language <i>Optional</i>. Default language to return in case of errors.
     * @return Language for the current user, $default_language on errors.
     */
    public function &getLanguageRedis($default_language = 'en') : string {

        // If redis or pdo connection are not set
        if (!isset($this->redis)) {

            // return default language
            return $default_language;

        }

        // Does it exists on redis?
        if ($this->redis->exists($this->chat_id . ':language')) {

            // Get the value
            $this->language = $this->redis->get($this->chat_id . ':language');
            return $this->language;

        }

        // If it doens't exist, set $language to $default_language
        $this->language = $default_language;

        // and return it
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
    public function &getLanguageRedisAsCache($default_language = 'en', $expiring_time = '86400') : string {

        // If redis or pdo connection are not set
        if (!isset($this->redis) || !isset($this->pdo)) {

            // return default language
            return $default_language;

        }

        // Does it exists on redis?
        if ($this->redis->exists($this->chat_id . ':language')) {

            // Get the value
            $this->language = $this->redis->get($this->chat_id . ':language');
            return $this->language;

        }

        // Set the value from the db
        $this->redis->setEx($this->chat_id . ':language', $expiring_time, $this->getLanguageDatabase($default_language));

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
    public function setLanguageRedisAsCache($language, $expiring_time = '86400') {

        // Check database connection
        if (!isset($this->database) && !isset($this->redis)) {
            throw new BotException('Database connection not set');
        }

        // Update the language in the database
        $sth = $this->pdo->prepare('UPDATE ' . $this->user_table . ' SET language = :language WHERE ' . $this->id_column . ' = :id');
        $sth->bindParam(':language', $language);
        $sth->bindParam(':id', $this->chat_id);

        try {

            $sth->execute();

        } catch (PDOException $e) {

            throw new BotException($e->getMessage());

        }

        // Destroy statement
        $sth = null;

        // Set the language in redis with expiring
        $this->redis->setEx($this->chat_id . ':language', $expiring_time, $language);

        // Set language in the bot variable
        $this->language = $language;
    }

    /** @} */

    /**
     * \addtogroup State
     * @{
     */

    /**
     * \brief Get current user status from redis and set it in status variable.
     * \details Throw exception if redis connection is missing.
     * @param $default_status <i>Optional</i>. The default status to return in case there is no status for the current user.
     * @return The status for the current user, $default_language if missing.
     */
    public function &getStatus($default_status = -1) : int {

        if (!isset($this->redis)) {

            throw new BotException('Redis connection not set');

        }

        if ($this->redis->exists($this->chat_id . ':status')) {

            $this->status = $this->redis->get($this->chat_id . ':status');

            return $this->status;

        }

        $this->redis->set($this->chat_id . ':status', $default_status);
        $this->status = $default_status;
        return $default_status;

    }

    /** \brief Set the status of the bot in both redis and $status.
     * \details Throw exception if redis connection is missing.
     * @param $status The new status of the bot.
     */
    public function setStatus($status) {

        $this->redis->set($this->chat_id . ':status', $status);

        $this->status = $status;

    }

    /** @} */

}
