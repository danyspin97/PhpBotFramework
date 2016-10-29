<?php

namespace DanySpin97\PhpBotFramework;

/**
 * \class Bot Bot
 * Class Bot to handle task like api request, or more specific api function(sendMessage, editMessageText, etc).
 * Usage example in webhook.php
 *
 */
class Bot extends CoreBot {

    /**
     * \modules Bot Bot
     * @{
     */
    /** \brief Text received in messages */
    public $text;

    /** \brief Data received in callback query */
    public $data;

    /** \brief Query sent by the user in the inline query */
    public $query;

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

    /** \brief Table contaning bot users data. */
    public $user_table;

    /** \brief Name of the column that represents the user id. */
    public $id_column;

    /** @} */

    /** \addtogroup State
     * @{
     */

    /** \brief <i>Optional</i>. Status of the bot to handle data inserting */
    public $status;

    /** @} */

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    /** \brief Offset used when asking new updates from telegram. */
    private $offset = 0;

    /** @} */

    /**
     * \addtogroup Bot
     * @{
     */

    /** \constructor Descruct the class */
    public function __destruct() {

        // Close redis connection if it is open
        if (isset($this->redis)) {
            $this->redis->close();
        }

    }

    /**
     * \brief Get chat id of the current user.
     * @return Chat id of the user.
     */
    public function &getChatID() {
        return $this->chat_id;
    }

    /**
     * \brief Set current chat id.
     * \details Change the chat id which the bot execute api methods.
     * @param $chat_id The new chat id to set.
     */
    public function setChatID($chat_id) {
        $this->chat_id = $chat_id;
    }

    /** @} */

    /**
     * \addtogroup Database
     * @{
     */

    /**
     * \brief Connect to the database.
     * \details Create a pdo reference and store it in the bot.
     *
     * @param $driver Database used.
     * @param $dbname Name of the database.
     * @param $user Username for login.
     * @param $password Password for login.
     * @param $parameters Parameter for the connection.
     * @return Newly created pdo reference.
     */
    public function &connectToDatabase($driver, $dbname, $user, $password, array $parameters = null) {

        // Connect
        $this->pdo = new \PDO("$driver:host=localhost;dbname=$dbname", $user, $password);

        if (isset($parameters)) {

            foreach ($parameters as $key => $value) {
                $this->pdo->setAttribute($key, $value);
            }

        } else {

            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
            $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        }

        return $this->pdo;

    }

    /**
     * \brief Get current user language from the database.
     * @param $default_language Default language to return in case of errors.
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
        $sth = $this->pdo->prepare('SELECT language FROM "User" WHERE "chat_id" = :chat_id');
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

        // If we couldn't get it
        } else {

            // Set the language to english
            $this->language = $default_language;
            // And return english
            return $this->language;

        }
    }

    /** @} */

    /**
     * \addtogroup Redis
     * @{
     */

    /**
     * \brief Get current user language from redis.
     * \details Using redis database we get language stored.
     * @param $default_language Default language to return in case of errors.
     * @return Language for the current user, $default_language on errors.
     */
    public function &getLanguageRedis($default_language = 'en') {

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

        } else {

            $this->language = 'en';
            return $this->language;

        }

    }

    /**
     * \brief Get current user language from redis, as a cache.
     * \details Using redis database as cache, seeks the language in it, if there isn't
     * then get the language from the sql database and store it (with default expiring of one day) in redis.
     * It also change $language parameter of the bot to the language returned.
     * @param $default_language Default language to return in case of errors.
     * @param $expiring_time Set the expiring time for the language on redis each time it is took from the sql database.
     * @return Language for the current user, $default_language on errors.
     */
    public function &getLanguageRedisAsCache($default_language = 'en', $expiring_time = '86400') {

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

        } else {

            // Set the value from the db
            $this->redis->setEx($this->chat_id . ':language', $expiring_time, $this->getLanguageDatabase());
            return $this->language;

        }

    }

    /**
     * \brief Set the current user language in both redis and sql database.
     * \details Save it on database first, then create the expiring key on redis.
     * @param $language The new language to set.
     * @param $expiring_time Time for the language key in redis to expire.
     * @return On sucess, return true, throw exception otherwise.
     */
    public function setLanguageRedisAsCache($language, $user_table = '"User"', $id_column = 'chat_id', $expiring_time = '86400') {

        // Check database connection
        if (!isset($this->database) && !isset($this->redis)) {
            throw new BotException('Database connection not set');
        }

        // Update the language in the database
        $sth = $this->pdo->prepare('UPDATE ' . $user_table . ' SET language = :language WHERE ' . $id_column . ' = :id');
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
     * \brief Get current user status from redis and set it in status variable.
     * @return The status.
     */
    public function &getStatus() {

        if (!isset($this->redis)) {
            throw new BotException('Redis connection not set');
        }

        $is_status_set = $this->redis->exists($this->chat_id . ':status');
        if ($is_status_set) {
            $this->status = $this->redis->get($this->chat_id . ':status');
            return $this->status;
        } else {
            $this->redis->set($this->chat_id . ':status', 0);
            $this->redis->set($this->chat_id . ':easter_egg', 1);
            $this->status = -1;
            return -1;
        }
    }

    // Set the status of the bot
    public function setStatus($status) {
        $this->redis->set($this->chat_id . ':status', $status);
    }

    // Read update and sent it to the right method
    public function processUpdate(&$update) {

        if (isset($update['message'])) {

            $this->chat_id = $update['message']['from']['id'];
            $this->text = $update['message']['text'];

            $this->processMessage($update['message']);

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

    protected function processMessage(&$message) {}

    protected function processCallbackQuery(&$callback_query) {}

    protected function processInlineQuery(&$inline_query) {}

    protected function processChosenInlineResult(&$chosen_inline_result) {}

    protected function processEditedMessage(&$edited_message) {}

    /*
     * Get updates received by the bot, save the new offset to Redis and then process them
     * (https://core.telegram.org/bots/api#getupdates)
     * @param
     * $variable_name Name of the variable where the offset is saved on Redis
     */
    public function getUpdatesRedis($limit = 100, $timeout = 0, $variable_name = 'offset') {
        if (!isset($this->redis)) {
            exit;
        }
        $offset = $this->redis->get($variable_name);
        $updates = $this->getUpdates($offset, $limit, $timeout);

        if (!empty($updates)) {
            foreach ($updates as $key => $update) {
                 $this->processUpdate($update);
            }
        
            $this->redis->set($variable_name, $offset + count($updates));
        }
    }

     public function getNextUpdates() {
        $url = $this->api_url . 'getUpdates';

        return $this->exec_curl_request($url);
     }

     public function getUpdatesLocal($limit = 100, $timeout = 60) {
         while(true) {
             $parameters = [
                 'offset' => &$this->offset,
                 'limit' => &$limit,
                 'timeout' => &$timeout
             ];
             $url = $this->api_url . 'getUpdates?' . http_build_query($parameters);
             $updates = $this->exec_curl_request($url);
             if (!empty($updates)) {
                 if ($this->offset === 0) {
                     $this->offset = $updates[0]['update_id'];
                 }
                 foreach($updates as $key => $update) {
                     $this->processUpdate($update);
                 }

                 $this->offset += sizeof($updates);
             }
         }
     }

     /*
     * Get updates received by the bot, save the new offset to the database and then process them
     * (https://core.telegram.org/bots/api#getupdates)
     * @param
     * $table_name Name of the table where offset is saved in the database
     * $column_name Name of the column where the offset is saved in the database
     */
    public function getUpdatesDatabase($limit = 100, $timeout = 0, $table_name = 'telegram', $column_name = 'bot_offset') {
        if (!isset($this->database)) {
            exit;
        }
        $sth = $this->pdo->prepare('SELECT "' . $column_name . '" FROM "' . $table_name . '"');
        $sth->execute();
        $offset = $sth->fetchColumn();
        $sth = null;
        $updates = $this->getUpdates($offset, $limit, $timeout);

        if (!empty($updates)) {
            foreach($updates as $key => $update) {
                $this->processUpdate($update);
            }

            $sth = $this->pdo->prepare('UPDATE "' . $table_name . '" SET "' . $column_name . '" = :new_offset');
            $new_offset = $offset + sizeof($updates);
            $sth->bindParam(':new_offset', $new_offset);
            $sth->execute();
        }
    }

    public function adjustOffsetDatabase($limit = 100, $timeout = 0, $table_name = 'TELEGRAM', $column_name = 'offset') {
        if (!isset($this->database)) {
            exit;
        }
        $sth = $this->pdo->prepare('SELECT "' . $column_name . '" FROM "' . $table_name . '"');
        $sth->execute();
        $offset = $sth->fetchColumn();
        $sth = null;
        $updates = $this->getUpdates($offset, $limit, $timeout);

        if (!empty($updates)) {
            foreach($updates as $key => $update) {
                $new_offset = $this->processUpdate($update);
            }

            $sth = $this->pdo->prepare('UPDATE "' . $table_name . '" SET "' . $column_name . '" = :new_offset');
            $new_offset++;
            $sth->bindParam(':new_offset', $new_offset);
            $sth->execute();
            return $new_offset;
        }
    }

    public function adjustOffsetRedis($limit = 100, $timeout = 0, $variable_name = 'offset') {
        if (!isset($this->redis)) {
            exit;
        }
        $offset = $this->redis->get($variable_name);
        $updates = $this->getUpdates($offset, $limit, $timeout);

        if (!empty($updates)) {
            $this->redis->set('error', 1);
            foreach ($updates as $key => $update) {
                try {
                    $new_offset = $this->processUpdate($update);
                    $this->redis->set($variable_name, $new_offset);
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $new_offset++;
                }
            }

            $new_offset++;

            $this->redis->set($variable_name, $new_offset);
            $this->redis->set('error', 0);
            return $new_offset;
       }
    }

    public function &sendMessageRef(&$text, $parse_mode = 'HTML', $disable_web_preview = true, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

     /*
     * Send a text message with an inline_keyboard (https://core.telegram.org/bots/api#sendmessage)
     * @param
     * $text Text of the message
     * $inline_keyboard Inlike keyboard array (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     * $parse_mode Parse mode of the message (https://core.telegram.org/bots/api#formatting-options)
     */
    public function &sendMessageKeyboard(&$text, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_preview = true, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => $parse_mode,
            'reply_markup' => &$inline_keyboard,
            'disable_web_page_preview' => $disable_web_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Send a text message with an inline keyboard replying to another one (https://core.telegram.org/bots/api#sendmessage)
     * @param
     * $text Text of the message
     * $message_id Id of the message the bot will reply
     * $inline_keyboard Inlike keyboard array (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     * $parse_mode Parse mode of the message (https://core.telegram.org/bots/api#formatting-options)
     */
    public function &sendReplyMessageKeyboard(&$text, &$inline_keyboard, &$message_id, $parse_mode = 'HTML', $disable_web_preview = true, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'reply_to_message_id' => &$message_id,
            'parse_mode' => $parse_mode,
            'reply_markup' => &$inline_keyboard,
            'disable_web_page_preview' => $disable_web_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &getChatRef(&$chat_id) {
        $parameters = [
            'chat_id' => &$chat_id,
        ];
        $url = $this->api_url . 'getChat?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Answer a callback_query, this function remove the updating cirle on an inline keyboard button
     * without showing any message to the user (https://core.telegram.org/bots/api#answercallbackquery)
     */
    public function &answerEmptyCallbackQuery() {
        $parameters = [
            'callback_query_id' => &$this->update['callback_query']['id'],
            'text' => ''
        ];
        $url = $this->api_url . 'answerCallbackQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &answerCallbackQueryRef(&$text, $show_alert = false) {
        $parameters = [
            'callback_query_id' => &$this->update['callback_query']['id'],
            'text' => &$text,
            'show_alert' => &$show_alert
        ];
        $url = $this->api_url . 'answerCallbackQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &editMessageTextRef(&$text, &$message_id, $parse_mode = 'HTML', $disable_web_preview = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'text' => &$text,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &editInlineMessageTextRef(&$text, &$inline_message_id, $parse_mode = 'HTML', $disable_web_preview = false) {
        $parameters = [
            'inline_message_id' => &$inline_message_id,
            'text' => &$text,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

     /*
     * Edit text and inline keyboard of a message(https://core.telegram.org/bots/api#editmessagetext)
     * @param
     * $text New text of the message
     * $message_id Identifier of the message to edit
     * $inline_keyboard Inlike keyboard array (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
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
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Edit text and inline keyboard of a message sent by inline query (https://core.telegram.org/bots/api#editmessagetext)
     * @param
     * $text New text of the message
     * $message_id Identifier of the message to edit
     */

    public function &editInlineMessageTextKeyboard(&$text, &$inline_keyboard, &$inline_message_id, $parse_mode = 'HTML', $disable_web_preview = false) {
        $parameters = [
            'inline_message_id' => &$inline_message_id,
            'text' => &$text,
            'reply_markup' => &$inline_keyboard,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &editMessageReplyMarkupRef(&$message_id, &$inline_keyboard) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'reply_markup' => &$inline_keyboard,
        ];
        $url = $this->api_url . 'editMessageReplyMarkup?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Edit only the inline keyboard of a message sent by the inline query (https://core.telegram.org/bots/api#editmessagereplymarkup)
     * @param
     * $message_id Identifier of the message to edit
     * $inline_keyboard Inlike keyboard array (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     */
    public function &editInlineMessageReplyMarkup($message_id, $inline_keyboard) {
        $parameters = [
            'inline_message_id' => &$message_id,
            'reply_markup' => &$inline_keyboard,
        ];
        $url = $this->api_url . 'editMessageReplyMarkup?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &editInlineMessageReplyMarkupRef(&$message_id, &$inline_keyboard) {
        $parameters = [
            'inline_message_id' => &$message_id,
            'reply_markup' => &$inline_keyboard,
        ];
        $url = $this->api_url . 'editMessageReplyMarkup?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
     * without showing any results to the user
     * @param
     * $switch_pm_text Text to show on the button
     */
    public function &answerEmptyInlineQuerySwitchPM($switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {
        $parameters = [
            'inline_query_id' => &$this->update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'cache_time' => $cache_time
        ];

        $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &answerInlineQuerySwitchPMRef(&$results, &$switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {
        $parameters = [
            'inline_query_id' => &$this->update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => &$switch_pm_parameter,
            'results' => &$results,
            'cache_time' => $cache_time
        ];

        $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
}
