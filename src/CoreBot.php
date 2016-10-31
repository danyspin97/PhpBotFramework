<?php

namespace DanySpin97\PhpBotFramework;


/**
 * \mainpage
 * \section Description
 * PhpBotFramework a lightweight framework for Telegram Bot API.
 * Designed to be fast and easy to use, it provides all the features a user need.
 * Take control of your bot using the command-handler or the update type based function.
 *
 * A quick example that say "Hello" every time the user send "/start":
 *
 *     namespace DanySpin97\PhpBotFramework;
 *     require './vendor/autoload.php';
 *     $bot = new Bot("token");
 *     $bot->addMessageCommand("/start", function($bot, $message) {
 *             $bot->sendMessage("Hello");
 *             });
 *     $bot->getUpdatesLocal();
 *
 * \section Features
 * - Designed to be the fast and easy to use
 * - Support for getUpdates and webhooks
 * - Support for the most important API methods
 * - Command-handle system
 * - Update type based processing
 * - Easy inline keyboard creation
 * - Inline query results handler
 * - Sql database support
 * - Redis support
 * - Support for multilanguage bot
 * - Support for bot state
 * - Highly documented
 *
 * \section Requirements
 * - <b>Php 7.0</b>
 * - <b>Php-mbstring</b>
 * - <b>Composer</b> (to install the framework)
 * - <b>SSL certificate</b> (<i>required by webhook</i>)
 * - <b>Web server</b> (<i>required by webhook</i>)
 *
 * \section Installation
 * In your project folder:
 *
 *     composer require danyspin97/php-bot-framework
 *     composer install
 *
 * \subsection Web-server
 * To use webhook for the bot, a web server and a SSL certificate are required.
 * Install one using your package manager (nginx or caddy reccomended).
 * To get a SSL certificate you can user [Let's Encrypt](https://letsencrypt.org/).
 *
 * \section Usage
 * Add the scripting by adding command (addMessageCommand()) or by creating a class that inherits Bot.
 *
 * \subsection getUpdates
 * The bot ask for updates to telegram server.
 * If you want to use getUpdates method to receive updates from telegram, add one of these function at the end of your bot:
 * - <code>getUpdatesLocal()</code>
 * - <code>getUpdatesDatabase()</code>
 * - <code>getUpdatesRedis()</code>
 *
 * The bot will process updates in a row, and will call </code>processUpdate()</code> for each.
 * getUpdates handling is single-threaded so there will be only one object that will process updates, so the connection will be opened at the creation and used for the entire life of the bot.
 *
 * \subsection Webhook
 * A web server will create an instance of the bot for every update received.
 * If you want to use webhook call <code>processWebhookUpdate()</code> at the end of your bot. The bot will get data from php://input and process it using <code>processUpdate()</code>.
 * Each instance of the bot will open its connection.
 *
 * \subsection Commands
 * Script how the bot will answer to messages containing commands (like <code>/start</code>).
 *
 *     $bot->addMessageCommand("/start", function($bot, $message) {
 *             $bot->sendMessage("I am your personal bot, try /help command");
 *             });
 *
 *     $help_function = function($bot, $message) {
 *         $bot->sendMessage("This is the help message")
 *         };
 *
 *     $bot->addMessageCommand("/help", $help_function);
 *
 * \subsection Bot-Intherited Inherit Bot Class
 * Create a new class that inherits Bot to handle all updates.
 *
 * EchoBot.php
 *
 *     class EchoBot extends DanySpin97\PhpBotFramework\Bot {
 *         protected function processMessage(&$message) {
 *             $this->sendMessage($this->getText());
 *         }
 *     }
 *
 * main.php
 *
 *     $bot = new EchoBot("token");
 *     $bot->processWebhookUpdate();
 *
 * Override these method to make your bot handle each update type:
 * - <code>processMessage(&$message)</code>
 * - <code>processCallbackQuery(&$callback_query)</code>
 * - <code>processInlineQuery(&$inline_query)</code>
 * - <code>processChosenInlineResult(&$chosen_inline_result)</code>
 * - <code>processEditedMessage(&$edited_message)</code>
 *
 * \subsection Multilanguage-section Multilanguage Bot
 * This framework offers method to develop a multi language bot.
 * Here's an example:
 *
 *     $bot->localization = [ 'en' =>
 *                                   [ 'Greetings_Msg' => 'Hello'],
 *                            'it' =>
 *                                   [ 'Greetings_Msg' => 'Ciao']];
 *
 *     $start_function = function($bot, $message) {
 *             $bot->sendMessage($this->localization[
 *                     $bot->getLanguageDatabase()]['Greetings_Msg'])
 *     };
 *
 *     $bot->addMessageCommand("start", $start_function);
 *
 * The bot will get the language from the database, then the bot will send the message localizated for the user.
 *
 * \ref Multilanguage [See here for more]
 *
 * \section Bot-created Bot using this framework
 * - [@MyAddressBookBot](https://telegram.me/myaddressbookbot)
 * - [@Giveaways_bot](https:(https://telegram.me/giveaways_bot)
 *
 * \section Authors
 * This framework was developed by Danilo Spinella.
 *
 * \section License
 * PhpBotFramework is released under GNU Lesser General Public License. You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the wrapper don't have to be.
 *
 */

/**
 * \class CoreBot
 * \brief Core of the framework
 * \details Contains data used by the bot to works, curl request handling, and all api methods (sendMessage, editMessageText, etc).
 */
class CoreBot {

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /** \brief Chat_id of the user that interacted with the bot */
    protected $chat_id;

    /** @} */

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    /** \brief The bot token (given by @BotFather). */
    private $token;

    /** \brief Url request (containing $token). */
    protected $api_url;

    /** \brief Curl connection for request. */
    public $ch;


    /**
     * \constructor Contrusct an empty bot
     * \details Construct a bot passing the token
     * @param $token Token given by @botfather
     */
    public function __construct(string $token) {

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

    /** @} */

    /**
     * \addtogroup Api Api Methods
     * @{
     */

    /**
     * \brief Request bot updates.
     * \details Request updates received by the bot using method getUpdates of Telegram API. [Api reference](https://core.telegram.org/bots/api#getupdates)
     * @param $offset <i>Optional</i>. Identifier of the first update to be returned. Must be greater by one than the highest among the identifiers of previously received updates. By default, updates starting with the earliest unconfirmed update are returned. An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id. The negative offset can be specified to retrieve updates starting from -offset update from the end of the updates queue. All previous updates will forgotten.
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     * @return Array of updates (can be empty).
     */
    protected function &getUpdates(int $offset = 0, int $limit = 100, int $timeout = 60) {

        $parameters = [
            'offset' => &$offset,
            'limit' => &$limit,
            'timeout' => &$timeout,
        ];

        return $this->exec_curl_request($this->api_url . 'getUpdates?' . http_build_query($parameters));

    }

    /**
     * \brief Send a text message.
     * \details Use this method to send text messages. [Api reference](https://core.telegram.org/bots/api#sendmessage)
     * @param $text Text of the message.
     * @param $reply_markup <i>Optional</i>. Reply_markup of the message.
     * @param $parse_mode <i>Optional</i>. Parse mode of the message.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @return On success,  the sent message.
     */
    public function &sendMessage($text, string $reply_markup = null, int $reply_to = null, string $parse_mode = 'HTML', bool $disable_web_preview = true, bool $disable_notification = false) {

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

        return $this->exec_curl_request($this->api_url . 'sendMessage?' . http_build_query($parameters));

    }

    /**
     * \brief Forward a message.
     * \details Use this method to forward messages of any kind. [Api reference](https://core.telegram.org/bots/api#forwardmessage)
     * @param $from_chat_id The chat where the original message was sent.
     * @param $message_id Message identifier (id).
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @return On success,  the sent message.
     */
    public function &forwardMessage($from_chat_id, int $message_id, bool $disable_notification = false) {

        if (!isset($this->chat_id)) {
            throw new BotException('(sendMessage) Chat id is not set');
        }

        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'from_chat_id' => &$from_chat_id,
            'disable_notification' => &$disable_notification
        ];

        return $this->exec_curl_request($this->api_url . 'forwardMessage?' . http_build_query($parameters));

    }

    /**
     * \brief Send a photo.
     * \details Use this method to send photos. [Api reference](https://core.telegram.org/bots/api#sendphoto)
     * @param $photo Photo to send, can be a file_id or a string referencing the location of that image.
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param $caption <i>Optional</i>. Photo caption (may also be used when resending photos by file_id), 0-200 characters.
     * @param $disable_notification <i>Optional<i>. Sends the message silently.
     * @return On success,  the sent message.
     */
    public function &sendPhoto($photo, string $reply_markup = null, string $caption = '', bool $disable_notification = false) {

        $parameters = [
            'chat_id' => &$this->chat_id,
            'photo' => &$photo,
            'caption' => &$caption,
            'reply_markup' => &$reply_markup,
            'disable_notification' => &$disable_notification,
        ];

        return $this->exec_curl_request($this->api_url . 'sendPhoto?' . http_build_query($parameters));

    }

    /**
     * \brief Send an audio.
     * \details Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .mp3 format. [Api reference](https://core.telegram.org/bots/api/#sendaudio)
     * Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
     * @param $audio Audio file to send. Pass a file_id as String to send an audio file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get an audio file from the Internet, or upload a new one using multipart/form-data.
     * @param $caption <i>Optional</i>. Audio caption, 0-200 characters.
     * @param reply_markup <i>Optional</i>. JSON-serialized object for keyboard.
     * @param $duration <i>Optional</i>. Duration of the audio in seconds.
     * @param $performer <i>Optional</i>. Performer.
     * @param $title <i>Optional</i>. Track name.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @param $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return On success, the sent message.
     */
    public function &sendAudio($audio, string $caption = null, string $reply_markup = null, int $duration = null, string $title = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => &$this->chat_id,
            'audio' => &$photo,
            'caption' => &$caption,
            'duration' => &$duration,
            'performer' => &$performer,
            'title' => &$title,
            'reply_to_message_id' => &$reply_to_message_id,
            'reply_markup' => &$reply_markup,
            'disable_notification' => &$disable_notification,
        ];

        return $this->exec_curl_request($this->api_url . 'sendAudio?' . http_build_query($parameters));

    }

    /**
     * \brief Send a document.
     * \details Use this method to send general files. [Api reference](https://core.telegram.org/bots/api/#senddocument)
     * @param $document File to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data.
     * @param <i>Optional</i>. Document caption (may also be used when resending documents by file_id), 0-200 characters.
     * @param $reply_markup
     * @param <i>Optional</i>. Sends the message silently.
     * @param <i>Optional</i>. If the message is a reply, ID of the original message.
     */
    public function &senddocument($document, string $caption = '', string $reply_markup = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => &$this->chat_id,
            'document' => &$photo,
            'caption' => &$caption,
            'reply_to_message_id' => &$reply_to_message_id,
            'reply_markup' => &$reply_markup,
            'disable_notification' => &$disable_notification,
        ];

        return $this->exec_curl_request($this->api_url . 'sendAudio?' . http_build_query($parameters));


    }


    /**
     * \brief Send a sticker
     * \details Use this method to send .webp stickers. [Api reference](https://core.telegram.org/bots/api/#sendsticker)
     * @param $sticker Sticker to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a .webp file from the Internet, or upload a new one using multipart/form-data.
     * @param $disable_notification Sends the message silently.
     * @param On success, the sent message.
     */
    public function &sendSticker($sticker, string $reply_markup = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => &$this->chat_id,
            'sticker' => &$sticker,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => &$reply_to_message_id,
            'reply_markup' => &$reply_markup
        ];

        return $this->exec_curl_request($this->api_url . 'sendSticker?' . http_build_query($parameters));

    }

    /**
     * \brief Say the user what action is the bot doing.
     * \details Use this method when you need to tell the user that something is happening on the bot's side. The status is set for 5 seconds or less (when a message arrives from your bot, Telegram clients clear its typing status). [Api reference](https://core.telegram.org/bots/api#sendchataction)
     * @param $action Type of action to broadcast. Choose one, depending on what the user is about to receive:
     * - <code>typing</code> for text messages
     * - <code>upload_photo</code> for photos
     * - <code>record_video</code> or <code>upload_video</code> for videos
     * - <code>record_audio</code> or <code>upload_audio</code> for audio files
     * - <code>upload_document</code> for general files
     * - <code>find_location</code> for location data
     * @return True on success.
     */
    public function &sendChatAction(string $action) : bool {

        $parameters = [
            'chat_id' => &$this->chat_id,
            'action' => &$action
        ];

        return $this->exec_curl_request($this->api_url . 'sendChatAction?' . http_build_query($parameters));

    }

    /**
     * \brief Get info about a chat.
     * \details Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.). [Api reference](https://core.telegram.org/bots/api#getchat)
     * @param Unique identifier for the target chat or username of the target supergroup or channel (in the format <code>@channelusername</code>)
     */
    public function &getChat($chat_id) {

        $parameters = [
            'chat_id' => &$chat_id,
        ];

        return $this->exec_curl_request($this->api_url . 'getChat?' . http_build_query($parameters));

    }

    /* \brief Answer a callback query
     * \details Remove the updating cirle on an inline keyboard button and showing a message/alert to the user.
     * It will always answer the current callback query.
     * @param $text <i>Optional</i>. Text of the notification. If not specified, nothing will be shown to the user, 0-200 characters.
     * @param $show_alert <i>Optional</i>. If true, an alert will be shown by the client instead of a notification at the top of the chat screen.
     * @param $url <i>Optional</i>. URL that will be opened by the user's client. If you have created a Game and accepted the conditions via @Botfather, specify the URL that opens your game – note that this will only work if the query comes from a callback_game button.
Otherwise, you may use links like telegram.me/your_bot?start=XXXX that open your bot with a parameter.
     * @return True on success.
     */
    public function &answerCallbackQuery($text = '', $show_alert = false, string $url) : bool {

        $parameters = [
            'callback_query_id' => &$this->update['callback_query']['id'],
            'text' => &$text,
            'show_alert' => &$show_alert,
            'url' => &$url
        ];

        return $this->exec_curl_request($this->api_url . 'answerCallbackQuery?' . http_build_query($parameters));

    }

    /**
     * \brief Edit text of a message sent by the bot.
     * \details Use this method to edit text and game messages sent by the bot. [Api reference](https://core.telegram.org/bots/api#editmessagetext)
     * @param $text New text of the message.
     * @param $message_id Unique identifier of the sent message.
     * @param $reply_markup Reply markup of the message will have (will be removed if this is null).
     * @param $parse_mode <i>Optional</i>. Send Markdown or HTML.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     */
    public function &editMessageText($text, int $message_id, $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = false) {

        if (!isset($this->chat_id)) {
            throw new BotException('(sendMessage) Chat id is not set');
        }

        $parameters = [
            'chat_id' => &$this->chat_id,
            'message_id' => &$message_id,
            'text' => &$text,
            'reply_markup' => &$reply_markup,
            'parse_mode' => &$parse_mode,
            'disable_web_page_preview' => &$disable_web_preview,
        ];

        return $this->exec_curl_request($this->api_url . 'editMessageText?' . http_build_query($parameters));

    }

    /**
     * \brief Edit text of a message sent via the bot.
     * \details Use this method to edit text messages sent via the bot (for inline queries). [Api reference](https://core.telegram.org/bots/api#editmessagetext)
     * @param $text New text of the message.
     * @param $inline_message_id  Identifier of the inline message.
     * @param $reply_markup Reply markup of the message will have (will be removed if this is null).
     * @param $parse_mode <i>Optional</i>. Send Markdown or HTML.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     */
    public function &editInlineMessageText($text, string $inline_message_id, string $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = false) {

        $parameters = [
           'inline_message_id' => &$inline_message_id,
           'text' => &$text,
           'reply_markup' => &$inline_keyboard,
           'parse_mode' => &$parse_mode,
           'disable_web_page_preview' => &$disable_web_preview,
       ];

       return $this->exec_curl_request($this->api_url . 'editMessageText?' . http_build_query($parameters));

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

    /**
     * \brief Exec any api request using this method.
     * \details Use this method for custom api calls using this syntax:
     *
     *     $param = [
     *             'chat_id' => $chat_id,
     *             'text' => 'Hello!'
     *     ];
     *     apiRequest("sendMessage", $param);
     *
     * @param $method The method to call.
     * @param $parameters Parameters to add.
     * @return Depends on api method.
     */
    public function &apiRequest(string $method, array $parameters) {

        return $this->exec_curl_request($this->api_url . $method.'?'. http_build_query($parameters));

    }

    /**
     * \addtogroup Core Core(internal)
     * @{
     */

    /** \brief Base core function to execute url request
     * @param $url The url to call using the curl session.
     */
    protected function &exec_curl_request($url) {

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

    /** @} */

}

/** @} */
