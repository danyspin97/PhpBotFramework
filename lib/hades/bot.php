<?php

/*
 *
 * Class Bot to handle task like api request, or more specific api function(sendMessage, editMessageText, etc).
 * Usage example in webhook.php
 *
 */


class Bot {
    // Token of the bot
    private $token;
    // Url for api requesting
    private $api_url;
    // Update reference
    protected $update;
    // Chat_id of the user that interacted with the bot
    protected $chat_id;
    // Inline Keyboard
    public Inline_keyboard $inline_keyboard;
    // Database connection using class Database (optional)
    public Database $database;
    // Pdo reference (optional)
    public PDO $pdo;
    // Redis connection (optional)
    public REDIS $redis;
    // Language and localitation and localitation for multi-language bot
    public $language;
    public $localization;


    // Contructor, simply put token bot in $token variable
    public function __construct($token) {
        $this->token = &$token;
        $this->api_url = 'https://api.telegram.org/bot' . $token . '/';
        
    }
    
    public function __destruct() {
        // Close database connection by deleting the reference
        $this->database = null;
        // Close redis connection if it is open
        if (isset($this->redis))
            $this->redis->close();
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
     
    /*
     * Get language for the current user, reading it from the database
     */
     public function &getLanguage() {
        if (!isset($this->database)) {
            exit;
        }
        $sth = $this->pdo->prepare('SELECT "language" FROM "User" WHERE "chat_id" = :chat_id');
        $sth->bindParam(':chat_id', $this->chat_id);
        $sth->execute();
        $row = $sth->fetch();
        $sth = null;
        if (isset($row['language'])) {
            $this->language = $row['language'];
            return $row['language'];
        } else {
            $this->language = 'en';
            return $this->language;
        }
     }
     
         /*
     * Using Redis as a cache we store language in both database and Redis, read it from redis if
     * it exists (the key will expire in 1 day after it is set) or read it from the database and
     * store it in Redis
     */
     public function &getLanguageRedis() {
        if (!isset($this->redis)) {
            exit;
        }
        $is_language_set = $this->redis->exists($this->chat_id . ':language');
        if ($is_language_set) {
            $this->language = $this->redis->get($this->chat_id . 'language');
            return $this->language;
        } else {
            // TODO User Database instead of $pdo
            $redis->setEx($chat_id . ':language', 86400, getLanguage());
            return $this->language;
        }  
     }
     
    /*
     * Set language for the current user, first it save it on db, then change it on redis if it exists
     * @param
     * $language New language
     */
    function setLanguage($language) {
        if (!isset($this->database)) {
            exit;
        }
        $sth = $this->pdo->prepare('UPDATE "User" SET "language" = :language WHERE "chat_id" = :chat_id');
        $sth->bindParam(':language', $language);
        $sth->bindParam(':chat_id', $this->chat_id);
        $sth->execute();
        $sth = null;
        if (isset($this->redis)) {
            $this->redis->setEx($chat_id . ':language', 86400, $language); 
        }
    }
    
    public function setLocalization(&$localization) {
        $this->localization = &$localization;
    }
    
    
    
    // Read update and sent it to the right method
    public function processUpdate(&$update) {
        $this->update = &$update;
        if (isset($update['message'])) {
            $this->processMessage();
        } elseif (isset($update['callback_query'])) {
            $this->processCallbackQuery($update['callback_query']);
        } elseif (isset($update['inline_query'])) {
            $this->processInlineQuery($update['inline_query']);
        } elseif (isset($update['chosen_inline_result'])) {
            $this->processInlineResult($update['chosen_inline_result']);
        }
    }
    
    protected function processMessage() {
        $message = &$this->update['message'];
        $this->chat_id = &$message['from']['id'];
    }
    
    protected function processCallbackQuery() {
        $callback_query = &$this->update['callback_query'];
        $this->chat_id = &$callback_query['from']['id'];
    }
    
    protected function processInlineQuery() {
        $inline_query = &$this->update['inline_query'];
        $this->chat_id = &$inline_query['from']['id'];
    }
    
    protected function processInlineResult() {
        $inline_result = &$this->update['chosen_inline_result'];
        $this->chat_id = &$inline_result['from']['id'];
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
        
        $this->redis->set($variable_name, $offset + count($updates) + 1);
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
        
        $sth = $this->pdo->prepare('UPDATE :table_name SET :column_name = :new_offset');
        $sth->bindParam(':column_name', $column_name);
        $sth->bindParam(':table_name', $table_name);
        $new_offset = $offset + count($updates) + 1;
        $sth->bindParam(':new_offset', $new_offset);
        $sth->execute();
    }
    
    /*
     * Send a text only message (https://core.telegram.org/bots/api#sendmessage)
     * @param
     * $text Text of the message
     * $inline_keyboard reply_markup of the message (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     * $parse_mode Parse mode of the message (https://core.telegram.org/bots/api#formatting-options)
     */
    public function &sendMessage($text, $inline_keyboard = null, $reply_to = null, $parse_mode = 'HTML', $disable_web_page_preview = true, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_page_preview,
            'reply_markup' => $inline_keyboard,
            'reply_to_message_id' => 
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    public function &sendMessageRef(&$text, $parse_mode = 'HTML', $disable_web_page_preview = true, $disable_notification = false) {
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

    public function &sendMessageKeyboard(&$text, &$inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = true, $disable_notification	= false) {
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
    
    public function &sendReplyMessage(&$text, &$message_id, $parse_mode = 'HTML', $disable_web_page_preview = true, $disable_notification = false) {
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
    public function &sendReplyMessageKeyboard(&$text, &$inline_keyboard, &$message_id, $parse_mode = 'HTML', $disable_web_page_preview = true, $disable_notification = false) {
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
     * $inline_keyboard reply_markup of the message (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     */
    public function &sendPhoto($photo, $inline_keyboard = null, $caption = '', $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'reply_markup' => &$inline_keyboard,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &sendPhotoRef(&$photo, $inline_keyboard = null, $caption = '', $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'reply_markup' => &$inline_keyboard,
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
    
    public function &sendPhotoKeyboard(&$photo, &$inline_keyboard, $caption = '', $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'reply_markup' => &$inline_keyboard,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_page_preview,
        ];
        $url = $this->api_url . 'editMessageText?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    /*
     *  Send sticker (https://core.telegram.org/bots/api#sendsticker)
     */
    public function &sendSticker($sticker, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'sticker' => &$sticker,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendSticker?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &sendStickerRef(&$sticker, $disable_notification = false) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'sticker' => &$sticker,
            'disable_notification' => $disable_notification
        ];
        $url = $this->api_url . 'sendSticker?' . http_build_query($parameters);

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
     * Return basic info about a user or a chat identified by a chat_id (https://core.telegram.org/bots/api#getchat)
     */
    public function &getChat($chat_id) {
        $parameters = [
            'chat_id' => &$chat_id,
        ];
        $url = $this->api_url . 'getChat?' . http_build_query($parameters);

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
    public function &editMessageText($text, $message_id, $inline_keyboard = null, $parse_mode = 'HTML', $disable_web_page_preview = false) {
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
    public function &editInlineMessageText($text, $inline_message_id, $inline_keyboard, $parse_mode = 'HTML', $disable_web_page_preview = false) {
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
    public function &editMessageTextKeyboard(&$text, &$inline_keyboard, &$message_id, $parse_mode = 'HTML', $disable_web_page_preview = false) {
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
    
    public function &editInlineMessageTextInlineKeyboard(&$text, &$inline_keyboard, &$inline_message_id, $parse_mode = 'HTML', $disable_web_page_preview = false) {
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
    public function &editMessageReplyMarkup($message_id, $inline_keyboard) {
        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'reply_markup' => &$inline_keyboard,
        ];
        $url = $this->api_url . 'editMessageReplyMarkup?' . http_build_query($parameters);

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
            'inline_query_id' => &$update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'cache_time' => $cache_time
        ];

        $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &answerEmptyInlineQuerySwitchPMRef(&$switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {
        $parameters = [
            'inline_query_id' => &$update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'cache_time' => $cache_time
        ];

        $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /*
     * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
     * @param
     * $results Array on InlineQueryResult (https://core.telegram.org/bots/api#inlinequeryresult)
     * $switch_pm_text Text to show on the button
     */
    public function &answerInlineQuerySwitchPM($results, $switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {
        $parameters = [
            'inline_query_id' => &$update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'results' => &$results,
            'cache_time' => $cache_time
        ];

        $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    public function &answerInlineQuerySwitchPMRef(&$results, &$switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {
        $parameters = [
            'inline_query_id' => &$update['inline_query']['id'],
            'switch_pm_text' => &$switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'results' => &$results,
            'cache_time' => $cache_time
        ];

        $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }
    
    function &apiRequest($method, $parameters) {
        if (!is_string($method)) {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            error_log("Parameters must be an array\n");
            return false;
        }

        $url = $this->api_url . $method.'?'. http_build_query($parameters);

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
