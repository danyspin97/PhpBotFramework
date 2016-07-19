<?php

/*
 *
 * Class Bot to handle task like api request, or more specific api function(sendMessage, editMessageText, etc).
 * Usage example in webhook.php
 *
 */


class Bot {
    // Token of the bot
    protected $token;
    // Url for api requesting
    protected $api_url;
    // Chat_id of the user that interacted with the bot
    protected $chat_id;
    // Database connection using class Database (optional)
    public $database;
    // Pdo reference (optional)
    public $pdo;
    // Redis connectio (optional)
    public $redis;
    // Language for multi-language bot
    public $language;
    // Update reference
    protected $update;

    // Contructor, simply put token bot in $token variable
    public function __construct($token) {
        $this->token = &$token;
        $this->api_url = 'https://api.telegram.org/bot' . $token . '/';
        
    }
    
    public function __destruct() {
        // Close database connection by deleting the reference
        $database = null;
        // Close redis connection if it is open
        if (isset($redis))
            $redis->close();
    }

    /*
     * Connect to database through the HadesSQL ORM,
     *
     * @param
     * $driver DBMS used
     * $dbname Name of the database
     * $user Username for logging
     * $password Passoword for the $username
     */
    public function &connectToDatabase($driver, $dbname, $user, $password) {
        $database = new Database($driver, $dbname, $user, $password);
        return $database;
    }
    
    // Connect to redis giving $address parameter and optional append port to it (127.0.0.1:6379)
    public function &connectToRedis($address = '127.0.0.1') {
        $redis = new Redis();
        $redis->connect($address);
        return $redis;
    }
    
    // Set chat_id of the bot
    public function setChatID($chat_id) {
        $this->chat_id = &$chat_id;
    }
    
    public function setChatIDRef(&$chat_id) {
        $this->chat_id = &$chat_id;
    }
    
    // Read update and sent it to the right method
    public function processUpdate(&$update) {
        $this->update = &$update;
        if (isset($update['message'])) {
            processMessage();
        } elseif (isset($update['callback_query'])) {
            processCallbackQuery($update['callback_query']);
        } elseif (isset($update['inline_query'])) {
            processInlineQuery($update['inline_query']);
        } elseif (isset($update['chosen_inline_result'])) {
            processInlineResult($update['chosen_inline_result']);
        }
    }
    
    protected function processMessage() {
        $message = &$update['message'];
    }
    
    protected function processCallbackQuery() {
        $callback_query = &$update['callback_query'];
    }
    
    protected function processInlineQuery() {
        $inline_query = &$update['inline_query'];
    }
    
    protected function processInlineResult() {
        $inline_result = &$update['chosen_inline_result'];
    }
    
    /*
     * Request updates received by the bot using method getUpdates of Telegram API
     * (https://core.telegram.org/bots/api#getupdates)
     */
    protected function &getUpdate($offset, $limit, $timeout) {
        $parameters = [
            'offset' => &$offset,
            'limit' => &$limit,
            'timeout' => &$limit,
        ];
        $url = $this->api_url . 'getUpdates?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
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
        $updates = &getUpdates($offset, $limit, $timeout);
        
        foreach($updates as $update) {
            processUpdate($update);
        }
    }
    
     /*
     * Get updates received by the bot, save the new offset to the database and then process them
     * (https://core.telegram.org/bots/api#getupdates)
     * @param
     * $table_name Name of the table where offset is saved in the database
     * $column_name Name of the column where the offset is saved in the database
     */
    public function getUpdatesDatabase($limit = 100, $timeout = 0, $table_name = 'TELEGRAM', $column_name = 'offset') {
        if (!isset($this->database)) {
                exit;
            }
            $sth = $this->pdo->prepare('SELECT :column_name FROM :table_name');
            $sth->bindParam(':column_name', $column_name);
            $sth->bindParam(':table_name', $table_name);
            $sth->execute();
            $offset = $sth->fetchColumn();
            $sth = null;
            $updates = &getUpdates($offset, $limit, $timeout);
            
            foreach($updates as $update) {
                processUpdate($update);
        }
    }
    
    /*
     * Send a text only message (https://core.telegram.org/bots/api#sendmessage)
     * @param
     * $text Text of the message
     * $parse_mode Parse mode of the message (https://core.telegram.org/bots/api#formatting-options)
     */
    public function &sendDefaultMessage($text, $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_page_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &sendDefaultMessageRef(&$text, $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_page_preview,
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
    public function &sendMessageKeyboard($text, $inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification	= false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => $parse_mode,
            'reply_markup' => &$inline_keyboard,
            'disable_web_page_preview' => $disable_web_page_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &sendMessageKeyboardRef(&$text, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification	= false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => $parse_mode,
            'reply_markup' => &$inline_keyboard,
            'disable_web_page_preview' => $disable_web_page_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
     /*
     * Send a text only message replying to another one (https://core.telegram.org/bots/api#sendmessage)
     * @param
     * $text Text of the message
     * $message_id Id of the message the bot will reply
     * $parse_mode Parse mode of the message (https://core.telegram.org/bots/api#formatting-options)
     */
    public function &sendReplyMessage($text, $message_id, $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'reply_to_message_id' => &$message_id,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_page_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &sendReplyMessageRef(&$text, &$message_id, $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'reply_to_message_id' => &$message_id,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_page_preview,
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
    public function &sendReplyMessageKeyboard($text, $message_id, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification	= false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'reply_to_message_id' => &$message_id,
            'parse_mode' => $parse_mode,
            'reply_markup' => &$inline_keyboard,
            'disable_web_page_preview' => $disable_web_page_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &sendReplyMessageKeyboardRef(&$text, &$message_id, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false, $disable_notification	= false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'reply_to_message_id' => &$message_id,
            'parse_mode' => $parse_mode,
            'reply_markup' => &$inline_keyboard,
            'disable_web_page_preview' => $disable_web_page_preview,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Forward a message (https://core.telegram.org/bots/api#forwardmessage)
     * @param
     * $from_chat_id The chat where the original message was sent
     * $message_id Message identifier (id)
     */
    public function &forwardMessage(&$from_chat_id, &$message_id, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'from_chat_id' => &$from_chat_id,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &forwardMessageRef($from_chat_id, $message_id, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'from_chat_id' => &$from_chat_id,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    /*
     * Send a photo (https://core.telegram.org/bots/api#sendphoto)
     * @param
     * $photo Photo to send, can be a file_id or a string referencing the location of that image
     */
    public function &sendPhoto($photo, $caption = '', $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &sendPhotoRef(&$photo, $caption = '', $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

     /*
     * Send a photo with an inline keyboard attached to it(https://core.telegram.org/bots/api#sendphoto)
     * @param
     * $photo Photo to send, can be a file_id or a string referencing the location of that image
     * $inline_keyboard Inlike keyboard array (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     */
    public function &sendPhotoKeyboard($photo, $inline_keyboard, $caption = '', $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &sendPhotoKeyboardRef(&$photo, &$inline_keyboard, $caption = '', $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Change the bot status for 5 or less seconds to $action (https://core.telegram.org/bots/api#sendchataction)
     */
    public function &sendChatAction($action) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'action' => &$action
        ];
        $url = $this->api_url . 'sendChatAction?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &sendChatActionRef(&$action) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'action' => &$action
        ];
        $url = $this->api_url . 'sendChatAction?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    /*
     * Answer a callback_query, this function remove the updating cirle on an inline keyboard button
     * without showing any message to the user (https://core.telegram.org/bots/api#answercallbackquery)
     */
    public function &answerEmptyCallbackQuery() {
        $parameters = [
            'id' => &$update['callback_query']['id'],
            'text' => ''
        ];
        $url = $this->api_url . 'answerCallbackQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Answer a callback_query, removing the updating cirle on an inline_keyboard and showing a message/alert to the user
     * @param
     * $text Text shown
     * $show_alert Show a alert or a simple notification on the top of the chat screen
     */
    public function &answerCallbackQuery(&$text, $show_alert = false) {
        $parameters = [
            'id' => &$update['callback_query']['id'],
            'text' => &$text,
            'show_alert' => &$show_alert
        ];
        $url = $this->api_url . 'answerCallbackQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &answerCallbackQueryRef($text, $show_alert = false) {
        $parameters = [
            'id' => &$update['callback_query']['id'],
            'text' => &$text,
            'show_alert' => &$show_alert
        ];
        $url = $this->api_url . 'answerCallbackQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Edit only text of a message (https://core.telegram.org/bots/api#editmessagetext)
     * @param
     * $text New text of the message
     * $message_id Identifier of the message to edit
     */
    public function &editMessageText($text, $message_id, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'text' => &$text,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &editMessageTextRef(&$text, &$message_id, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'text' => &$text,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

     /*
     * Edit only text of a message sent by inline query (https://core.telegram.org/bots/api#editmessagetext)
     * @param
     * $text New text of the message
     * $message_id Identifier of the message to edit
     */
    public function &editInlineMessageText($text, $inline_message_id, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        $parameters = [
            'inline_message_id' => &$inline_message_id,
            'text' => &$text,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &editInlineMessageTextRef(&$text, &$inline_message_id, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        $parameters = [
            'inline_message_id' => &$inline_message_id,
            'text' => &$text,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
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
    public function &editMessageTextInlineKeyboard($text, $message_id, $inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'text' => &$text,
            'reply_markup' => &$inline_keyboard,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &editMessageTextInlineKeyboardRef(&$text, &$message_id, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'text' => &$text,
            'reply_markup' => &$inline_keyboard,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
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
    public function &editInlineMessageTextInlineKeyboard(&$text, &$inline_message_id, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false) {
        $parameters = [
            'inline_message_id' => &$inline_message_id,
            'text' => &$text,
            'reply_markup' => &$inline_keyboard,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Edit only the inline keyboard of a message (https://core.telegram.org/bots/api#editmessagereplymarkup)ù
     * @param
     * $message_id Identifier of the message to edit
     * $inline_keyboard Inlike keyboard array (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     */
    public function &editMessageReplyMarkup(&$message_id, &$inline_keyboard) {
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
     * @param
     * $results Array on InlineQueryResult (https://core.telegram.org/bots/api#inlinequeryresult)
     * $switch_pm_text Text to show on the button
     */
    public function &answerInlineQuerySwitchPM($results, $switch_pm_text, $is_personal = true, $cache_time = 300) {
        $parameters = [
            'inline_query_id' => &$update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => 'show_ab',
            'results' => &$results,
            'cache_time' => $cache_time
        ];

        $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &answerInlineQuerySwitchPMRef(&$results, &$switch_pm_text, $is_personal = true, $cache_time = 300) {
        $parameters = [
            'inline_query_id' => &$update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => 'show_ab',
            'results' => &$results,
            'cache_time' => $cache_time
        ];

        $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    // Base core function to execute url request
    protected function &exec_curl_request(&$url) {
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($handle);

        if ($response === false) {
            $errno = curl_errno($handle);
            $error = curl_error($handle);
            error_log("Curl returned error $errno: $error\n");
            curl_close($handle);
            return false;
        }

        $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
        curl_close($handle);

        if ($http_code >= 500) {
            // do not wat to DDOS server if something goes wrong
            sleep(10);
            return false;
        } else if ($http_code != 200) {
            $response = json_decode($response, true);
            error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
            if ($http_code == 401) {
                throw new Exception('Invalid access token provided');
            }
            return false;
        } else {
            $response = json_decode($response, true);
            if (isset($response['desc'])) {
                error_log("Request was successfull: {$response['description']}\n");
            }
            $response = $response['result'];
        }
      return $response;
    }

}
