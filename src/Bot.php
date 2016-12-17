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
     * \addtogroup Bot Bot
     * \brief Properties and methods to handle the TelegramBot.
     * \details Here are listed all the properties and methods that will help the developer create the basic bot functions.
     * @{
     */

    /** \brief Text received in messages */
    private $text;

    /** \brief Data received in callback query */
    private $data;

    /** \brief Query sent by the user in the inline query */
    private $query;

    /** \brief Store the inline keyboard */
    public $keyboard;

    /** \brief Pdo reference */
    public $pdo;

    /** \brief Redis connection */
    public $redis;

    /** @} */

    /**
     * \addtogroup Core Core(internal)
     * @{
     */

    /** \brief Store the command triggered on message. */
    private $message_commands;

    /** \brief Does the bot has message commands? Set by initBot. */
    private $message_commands_set;

    /** \brief Store the command triggered on callback query. */
    private $callback_commands;

    /** \brief Does the bot has message commands? Set by initBot. */
    private $callback_commands_set;

    /** @} */

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
        $callback_commands = [];

        $this->keyboard = new InlineKeyboard($this);

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
     * \brief Get the query received from the inline query (for updates of type "inline_query").
     * @return The query sent by the user, throw exception if the current update is not an inline query.
     */
    public function getQuery() {

        if (isset($this->query)) {

            return $this->query;

        }

        throw new BotException("Query from inline query is not set: wrong update type");
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
     * \brief Init variables to skip parsing commands if there aren't any.
     * \details Called internnaly by
     * - <code>getUpdatesLocal</code>
     * - <code>getUpdatesRedis</code>
     * - <code>getUpdatesDatabase</code>
     * - <code>processWebhookUpdate</code>
     */
    private function initBot() {

        // Are there message commands?
        $this->message_commands_set = !empty($this->message_commands);

        // Are there callback commands?
        $this->callback_commands_set = !empty($this->callback_commands);

    }

    /**
     * \brief Dispatch each update to the right method (processMessage, processCallbackQuery, etc).
     * \details Set $chat_id for each update, $text, $data and $query are set for each update that contains them.
     * It also calls commands for each updates, before process methods.
     * @param $update Reference to the update received.
     * @return The id of the update processed.
     */
    public function processUpdate(array $update) : int {

        if (isset($update['message'])) {

            // Set data from the message
            $this->chat_id = $update['message']['chat']['id'];

            // If there are commands set by the user
            // and there are bot commands in the message, checking message entities
            if ($this->message_commands_set && isset($update['message']['entities']) && $update['message']['entities'][0]['type'] === 'bot_command') {

                // The lenght of the command
                $length = $update['message']['entities'][0]['length'];

                // Offset of the command
                $offset = $update['message']['entities'][0]['offset'];

                // For each command added by the user
                foreach ($this->message_commands as $trigger) {

                    // If the current command is a regex
                    if ($trigger['regex_active']) {

                        // Use preg_match to check if it is true
                        $matched = preg_match('/' . $trigger['regex_rule'] . '/', substr($update['message']['text'], $offset + 1, $length));

                    // else check if the command sent by the user is the same as the one we are expecting
                    } else if ($trigger['length'] == $length && mb_strpos($trigger['command'], $update['message']['text'], $offset) !== false) {

                        // We found a valid command
                        $matched = true;

                    } else {

                        // We did not
                        $matched = false;

                    }

                    // Check the results for the current command
                    if ($matched) {

                        // Execute script,
                        $trigger['script']($this, $update['message']);

                        // clear text variable
                        unset($this->text);

                        // and return the id of the current update to stop processing this update
                        return $update['update_id'];

                    }

                }

            }

            // And process it
            $this->processMessage($update['message']);

        } elseif (isset($update['callback_query'])) {
            // If the update is a callback query

            // Set variables
            $this->chat_id = $update['callback_query']['from']['id'];

            // Check for callback commands
            if (isset($update['callback_query']['data']) && $this->callback_commands_set) {

                // Parse all commands
                foreach ($this->callback_commands as $trigger) {

                    // If command is found in callback data
                    if (mb_strpos($trigger['data'], $update['callback_query']['data']) !== false) {

                        // Trigger the script
                        $trigger['script']($this, $update['callback_query']);

                        // Clear data
                        unset($this->data);

                        // and return the id of the current update
                        return $update['update_id'];

                    }

                }

            }

            // Process the callback query through processCallbackQuery
            $this->processCallbackQuery($update['callback_query']);

        } elseif (isset($update['inline_query'])) {

            $this->chat_id = $update['inline_query']['chat']['id'];
            $this->query = $update['inline_query']['query'];

            $this->processInlineQuery($update['inline_query']);

            unset($this->query);

        } elseif (isset($update['channel_post'])) {

            // Set data from the post
            $this->chat_id = $update['channel_post']['chat']['id'];

            $this->processChannelPost($update['channel_post']);

        } elseif (isset($update['edited_message'])) {

            $this->chat_id = $update['edited_message']['chat']['id'];

            $this->processEditedMessage($update['edited_message']);

        } elseif (isset($update['edited_channel_post'])) {

            $this->chat_id = $update['edited_channel_post']['chat']['id'];

            $this->processEditedChannelPost($update['edited_channel_post']);

        } elseif (isset($update['chosen_inline_result'])) {

            $this->chat_id = $update['chosen_inline_result']['chat']['id'];

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
     * <code>$chat_id</code> set inside of this function.
     * @param $message Reference to the message received.
     */
    protected function processMessage($message) {}

    /**
     * \brief Called every callback query received by the bot.
     * \details Override it to script the bot answer for each callback.
     * <code>$chat_id</code> set inside of this function.
     * @param $callback_query Reference to the callback query received.
     */
    protected function processCallbackQuery($callback_query) {}

    /**
     * \brief Called every inline query received by the bot.
     * \details Override it to script the bot answer for each inline query.
     * $chat_id and $query(use getQuery() to access it) set inside of this function.
     * @param $inline_query Reference to the inline query received.
     */
    protected function processInlineQuery($inline_query) {}

    /**
     * \brief Called every chosen inline result received by the bot.
     * \details Override it to script the bot answer for each chosen inline result.
     * <code>$chat_id</code> set inside of this function.
     * @param $chosen_inline_result Reference to the chosen inline result received.
     */
    protected function processChosenInlineResult($chosen_inline_result) {}

    /**
     * \brief Called every chosen edited message received by the bot.
     * \details Override it to script the bot answer for each edited message.
     * <code>$chat_id</code> set inside of this function.
     * @param $edited_message The message edited by the user.
     */
    protected function processEditedMessage($edited_message) {}

    /**
     * \brief Called every new post in the channel where the bot is in.
     * \details Override it to script the bot answer for each post sent in a channel.
     * <code>$chat_id</code> set inside of this function.
     * @param $post The message sent in the channel.
     */
    protected function processChannelPost($post) {}

    /**
     * \brief Called every time a post get edited in the channel where the bot is in.
     * \details Override it to script the bot answer for each post edited  in a channel.
     * <code>$chat_id</code> set inside of this function.
     * @param $post The message edited in the channel.
     */
    protected function processEditedChannelPost($edited_post) {}

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
                    'offset' => $offset,
                    'limit' => $limit,
                    'timeout' => $timeout
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
    public function addMessageCommand(string $command, callable $script) {

        $this->message_commands[] = [
                'script' => $script,
                'command' => '/' . $command,
                'length' => strlen($command) + 1,
                'regex_active' => false
        ];

    }

    /**
     * \brief Add a function that will be executed everytime a message contain a command that match the regex
     * \details Use this syntax:
     *
     *     addMessageCommandRegex("number\d", function($bot, $message, $result) {
     *         $bot->sendMessage("You sent me a number"); });
     * @param $regex_rule Regex rule that will called for evalueting the command received.
     * @param $script The function that will be triggered by a command. Must take an object(the bot) and an array(the message received).
     */
    public function addMessageCommandRegex(string $regex_rule, callable $script) {

        $this->message_commands[] = [
                'script' => $script,
                'regex_active' => true,
                'regex_rule' => $regex_rule
        ];

    }

    /**
     * \brief Add a function that will be executed everytime a callback query contains a string as data
     * \details Use this syntax:
     *
     *     addMessageCommand("menu", function($bot, $callback_query) {
     *         $bot->editMessageText($callback_query['message']['message_id'], "This is the menu"); });
     * @param $data The string that will trigger this function.
     * @param $script The function that will be triggered by the callback query if it contains the $data string. Must take an object(the bot) and an array(the callback query received).
     */
    public function addCallbackCommand(string $data, callable $script) {

        $this->callback_commands[] = [
                'data' => $data,
                'script' => $script,
        ];

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
    public function getLanguageDatabase($default_language = 'en') {

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
    public function getLanguageRedis($default_language = 'en') : string {

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
    public function getLanguageRedisAsCache($default_language = 'en', $expiring_time = '86400') : string {

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

    /**
     * \addtogroup State
     * \brief Create a state based bot using these methods.
     * \details Bot will answer in different way based on the state.
     * Here is an example where we use save user credential using bot states:
     *
     *     <?php
     *
     *     // Include the framework
     *     require './vendor/autoload.php';
     *
     *     // Define bot state
     *     define("SEND_USERNAME", 1);
     *     define("SEND_PASSWORD", 2);
     *
     *     // Create the class for the bot that will handle login
     *     class LoginBot extends DanySpin97\PhpBotFramework\Bot {
     *
     *         // Add the function for processing messages
     *         protected function processMessage($message) {
     *
     *             switch($this->getStatus()) {
     *
     *                 // If we are expecting a username from the user
     *                 case SEND_USERNAME:
     *
     *                     // Save the username
     *
     *                     // Say the user to insert the password
     *                     $this->sendMessage("Please, send your password.");
     *
     *                     // Update the bot state
     *                     $this->setStatus(SEND_PASSWORD);
     *
     *                     break;
     *
     *                 // Or if we are expecting a password from the user
     *                 case SEND_PASSWORD:
     *
     *                     // Save the password
     *
     *                     // Say the user he completed the process
     *                     $this->sendMessage("The registration is complete");
     *
     *                     break;
     *                 }
     *
     *         }
     *
     *     }
     *
     *     // Create the bot
     *     $bot = new LoginBot("token");
     *
     *     // Create redis object
     *     $bot->redis = new Redis();
     *
     *     // Connect to redis database
     *     $bot->redis->connect('127.0.0.1');
     *
     *     // Create the awnser to the <code>/start</code> command
     *     $start_closure = function($bot, $message) {
     *
     *         // saying the user to enter a username
     *         $bot->sendMessage("Please, send your username.");
     *
     *         // and update the status
     *         $bot->setStatus(SEND_USERNAME);
     *     };
     *
     *     // Add the answer
     *     $bot->addMessageCommand("start", $start_closure);
     *
     *     $bot->getUpdatesLocal();
     * @{
     */

    /**
     * \brief Get current user status from redis and set it in status variable.
     * \details Throw exception if redis connection is missing.
     * @param $default_status <i>Optional</i>. The default status to return in case there is no status for the current user.
     * @return The status for the current user, $default_status if missing.
     */
    public function getStatus(int $default_status = -1) : int {

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
    public function setStatus(int $status) {

        $this->redis->set($this->chat_id . ':status', $status);

        $this->status = $status;

    }

    /** @} */

    /**
     * \addtogroup Users-handle Users handling
     * \brief Handle bot users on the database.
     * @{
     */

    /** \brief Add a user to the database.
     * \details Add a user to the database in Bot::$user_table table and Bot::$id_column column using Bot::$pdo connection.
     * @param $chat_id chat_id of the user to add.
     * @return True on success.
     */
    public function addUser($chat_id) : bool {

        // Is there database connection?
        if (!isset($this->pdo)) {

            throw new BotException("Database connection not set");

        }

        // Create insertion query and initialize variable
        $query = "INSERT INTO $this->user_table ($this->id_column) VALUES (:chat_id)";

        // Prepare the query
        $sth = $this->pdo->prepare($query);

        // Add the chat_id to the query
        $sth->bindParam(':chat_id', $chat_id);

        try {

            $sth->execute();
            $success = true;

        } catch (PDOException $e) {

            echo $e->getMessage();

            $success = false;

        }

        // Close statement
        $sth = null;

        // Return result
        return $success;

    }

    /**
     * \brief Broadcast a message to all user registred on the database.
     * \details Send a message to all users subscribed, change Bot::$user_table and Bot::$id_column to match your database structure is.
     * This method requires Bot::$pdo connection set.
     * All parameters are the same as CoreBot::sendMessage.
     * Because a limitation of Telegram Bot API the bot will have a delay after 20 messages sent in different chats.
     * @see CoreBot::sendMessage
     */
    public function broadcastMessage($text, string $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = true, bool $disable_notification = false) {

        // Is there database connection?
        if (!isset($this->pdo)) {

            throw new BotException("Database connection not set");

        }

        // Prepare the query to get all chat_id from the database
        $sth = $this->pdo->prepare("SELECT $this->id_column FROM $this->user_table");

        try {

            $sth->execute();

        } catch (PDOException $e) {

            echo $e->getMessage();

        }

        // Iterate over all the row got
        while($user = $sth->fetch()) {

            // Call getChat to know that this users haven't blocked the bot
            $user_data = $this->getChat($user[$this->id_column]);

            // Did they block it?
            if ($user_data !== false) {

                // Change the chat_id for the next API method
                $this->setChatID($user[$this->id_column]);

                // Send the message
                $this->sendMessage($text, $reply_markup, null, $parse_mode, $disable_web_preview);

            }

        }

        // Close statement
        $sth = null;

    }

    /** @} */

}
