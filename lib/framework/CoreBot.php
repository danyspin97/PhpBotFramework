<?php

namespace DanySpin97\PhpBotFramework;


/**
 * \mainpage
 * \section Description
 * PhpBotFramework a lightweight framework for Telegram Bot API.
 * Designed to be fast and easy to use, it provides all the features a user need.
 * Take control of your bot using the command-handler or the update type based function.
 *
 * \subsection Features
 * - Designed to be the fast and easy to use
 * - Support for getUpdates and webhooks
 * - Support all API methods
 * - Command-handle system
 * - Update type based processing
 * - Easy inline keyboard creation
 * - Inline query results handler
 * - Sql database support
 * - Redis support
 * - Highly tested
 * - Highly documented
 *
 */

/**
 * \brief Core of the framework
 * \details Contains data used by the bot to works, curl request handling, and all api methods (sendMessage, editMessageText, etc).
 *
 */

class CoreBot {

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    /** \brief The bot token */
    private $token;

    /** \brief Url request (containing $token) */
    protected $api_url;

    // Chat_id of the user that interacted with the bot
    protected $chat_id;

    // Curl request handler
    public $ch;

    /** @} */

    /** \brief Contrusct an empty bot
     * \details Construct a bot passing the token
     * @param $token Token given by @botfather
     */
    public function __construct($token) {

        // Check token is valid
        if (is_numeric($token) || $token === '') {
            throw new BotException('Token is not valid or empty');
            return;
        }

        // Init variables
        $this->token = &$token;
        $this->api_url = 'https://api.telegram.org/bot' . $token . '/';

        // Init connection and config it
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_ENCODING,  '');
    }

    /** \brief Destroy the object */
    public function __destruct() {
        // Close connection
        curl_close($this->ch);
    }

    /**
     * \addtogroup Api Methods
     * @{
     */

    /**
     * \brief Request bot updates.
     * \details Request updates received by the bot using method getUpdates of Telegram API.
     * (https://core.telegram.org/bots/api#getupdates)
     * @param $offset <i>Optional</i>. Identifier of the first update to be returned. Must be greater by one than the highest among the identifiers of previously received updates. By default, updates starting with the earliest unconfirmed update are returned. An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id. The negative offset can be specified to retrieve updates starting from -offset update from the end of the updates queue. All previous updates will forgotten.
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     * @return <i>Optional</i>. An Array of Update objects is returned.
     */
    protected function &getUpdates($offset = 0, $limit = 100, $timeout = 60) {

        $parameters = [
            'offset' => &$offset,
            'limit' => &$limit,
            'timeout' => &$timeout,
        ];
        $url = $this->api_url . 'getUpdates?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /**
     * \brief Send a text message.
     * \details Use this method to send text messages. (https://core.telegram.org/bots/api#sendmessage)
     * @param $text Text of the message.
     * @param <i>Optional</i>. $inline_keyboard reply_markup of the message (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     * @param $parse_mode <i>Optional</i>. Parse mode of the message (https://core.telegram.org/bots/api#formatting-options)
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @return On success, the sent message.
     */
    public function &sendMessage($text, $reply_markup = null, $reply_to = null, $parse_mode = 'HTML', $disable_web_preview = true, $disable_notification = false) {

        if (!isset($this->chat_id)) {
            throw new BotException('(sendMessage) Chat id is not set');
        }

        $parameters = [
            'chat_id' => &$this->chat_id,
            'text' => &$text,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_preview,
            'reply_markup' => &$reply_markup,
            'reply_to_message_id' => &$reply_to,
            'disable_notification' => &$disable_notification
        ];

        $url = $this->api_url . 'sendMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /**
     * \brief Forward a message.
     * \details Use this method to forward messages of any kind. (https://core.telegram.org/bots/api#forwardmessage)
     * @param $from_chat_id The chat where the original message was sent.
     * @param $message_id Message identifier (id).
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @return On success, the sent message.
     */
    public function &forwardMessage($from_chat_id, $message_id, $disable_notification = false) {

        if (!isset($this->chat_id)) {
            throw new BotException('(sendMessage) Chat id is not set');
        }

        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'from_chat_id' => &$from_chat_id,
            'disable_notification' => &$disable_notification
        ];

        $url = $this->api_url . 'forwardMessage?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /**
     * \brief Send a photo.
     * \details Use this method to send photos. (https://core.telegram.org/bots/api#sendphoto)
     * @param $photo Photo to send, can be a file_id or a string referencing the location of that image.
     * @param $inline_keyboard reply_markup of the message (https://core.telegram.org/bots/api#inlinekeyboardmarkup).
     * @param $caption Photo caption (may also be used when resending photos by file_id), 0-200 characters.
     * @param $disable_notification Sends the message silently.
     * @return On success, the sent message.
     */
    public function &sendPhoto($photo, $reply_markup = null, $caption = '', $disable_notification = false) {

        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'reply_markup' => &$reply_markup,
            'disable_notification' => &$disable_notification,
        ];

        $url = $this->api_url . 'sendPhoto?' . http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    /**
     * \brief Send an audio.
     * \details Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .mp3 format. (https://core.telegram.org/bots/api/#sendaudio)
     * @param
     */
    public function &sendAudio($audio, $caption = null, $reply_markup = null, $duration = null, $title = null, $disable_notification = false, $reply_to_message_id = null) {

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

    /*
     * Answer a callback_query, removing the updating cirle on an inline_keyboard and showing a message/alert to the user
     * @param
     * $text Text shown
     * $show_alert Show a alert or a simple notification on the top of the chat screen
     */
    public function &answerCallbackQuery($text, $show_alert = false) {

        $parameters = [
            'callback_query_id' => &$this->update['callback_query']['id'],
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
    public function &editMessageText($text, $message_id, $inline_keyboard = null, $parse_mode = 'HTML', $disable_web_preview = false) {

        if (!isset($this->chat_id)) {
            throw new BotException('(sendMessage) Chat id is not set');
        }

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
    * Edit only text of a message sent by inline query (https://core.telegram.org/bots/api#editmessagetext)
    * @param
    * $text New text of the message
    * $message_id Identifier of the message to edit
    */
    public function &editInlineMessageText($text, $inline_message_id, $inline_keyboard, $parse_mode = 'HTML', $disable_web_preview = false) {

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

   /*
    * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
    * @param
    * $results Array on InlineQueryResult (https://core.telegram.org/bots/api#inlinequeryresult)
    * $switch_pm_text Text to show on the button
    */
    public function &answerInlineQuerySwitchPM($results, $switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {

       $parameters = [
           'inline_query_id' => &$this->update['inline_query']['id'],
           'switch_pm_text' => &$switch_pm_text,
           'is_personal' => $is_personal,
           'switch_pm_parameter' => $switch_pm_parameter,
           'results' => &$results,
           'cache_time' => $cache_time
       ];

       $url = $this->api_url . 'answerInlineQuery?' . http_build_query($parameters);

       return $this->exec_curl_request($url);
    }

    public function &apiRequest($method, $parameters) {

        if (!is_string($method)) {
            throw new BotException('Method name must be a string');
        }

        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            throw new BotException('Parameters must be an array');
        }

        $url = $this->api_url . $method.'?'. http_build_query($parameters);

        return $this->exec_curl_request($url);
    }

    // Base core function to execute url request
    protected function &exec_curl_request(&$url) {

        curl_setopt($this->ch, CURLOPT_URL, $url);

        $response = curl_exec($this->ch);

        if ($response === false) {
            $errno = curl_errno($this->ch);
            $error = curl_error($this->ch);
            error_log("Curl returned error $errno: $error\n");
            return false;
        }

        $http_code = intval(curl_getinfo($this->ch, CURLINFO_HTTP_CODE));

        if ($http_code === 200) { 
            $response = json_decode($response, true);
            if (isset($response['desc'])) {
                error_log("Request was successfull: {$response['description']}\n");
            }
            return $response['result'];
        } elseif ($http_code >= 500) {
            // do not wat to DDOS server if something goes wrong
            sleep(10);
            return false;
        } elseif ($http_code !== 200) {
            $response = json_decode($response, true);
            error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
            if ($http_code === 401) {
                throw new BotException('Invalid access token provided');
            }
            return false;
        }

        return $response;
    }
}

/** @} */
