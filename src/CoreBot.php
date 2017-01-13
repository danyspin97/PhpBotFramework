<?php

namespace DanySpin97\PhpBotFramework;


/**
 * \mainpage
 * \section Description
 * PhpBotFramework a lightweight framework for Telegram Bot API.
 * Designed to be fast and easy to use, it provides all the features a user need.
 * Take control of your bot using the command-handler system or the update type based function.
 *
 * \subsection Example
 * A quick example, the bot will send "Hello" every time the user click "/start":
 *
 *     <?php
 *
 *     // Include the framework
 *     require './vendor/autoload.php';
 *
 *     // Create the bot
 *     $bot = new DanySpin97\PhpBotFramework\Bot("token");
 *
 *     // Add a command that will be triggered every time the user click /start
 *     $bot->addMessageCommand("start",
 *         function($bot, $message) {
 *             $bot->sendMessage("Hello");
 *         }
 *     );
 *
 *     // Receive update from telegram using getUpdates
 *     $bot->getUpdatesLocal();
 *
 * \section Features
 * - Designed to be the fast and easy to use
 * - Support for getUpdates and webhooks
 * - Support for the most important API methods
 * - Command-handle system for messages and callback queries
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
 * - Php 7.0 or greater
 * - php-mbstring
 * - Composer (to install the framework)
 * - SSL certificate (<i>required by webhook</i>)
 * - Web server (<i>required by webhook</i>)
 *
 * \section Installation
 * In your project folder:
 *
 *     composer require danyspin97/php-bot-framework
 *     composer install --no-dev
 *
 * \subsection Web-server
 * To use webhook for the bot, a web server and a SSL certificate are required.
 * Install one using your package manager (nginx or caddy reccomended).
 * To get a SSL certificate you can user [Let's Encrypt](https://letsencrypt.org/).
 *
 * \section Usage
 * Add the scripting by adding command (Bot::addMessageCommand()) or by creating a class that inherits Bot.
 * Each api call will have <code>$chat_id</code> set to the current user, use CoreBot::setChatID() to change it.
 *
 * \subsection getUpdates
 * The bot ask for updates to telegram server.
 * If you want to use getUpdates method to receive updates from telegram, add one of these function at the end of your bot:
 * - Bot::getUpdatesLocal()
 * - Bot::getUpdatesDatabase()
 * - Bot::getUpdatesRedis()
 *
 * The bot will process updates in a row, and will call Bot::processUpdate() for each.
 * getUpdates handling is single-threaded so there will be only one object that will process updates. The connection will be opened at the creation and used for the entire life of the bot.
 *
 * \subsection Webhook
 * A web server will create an instance of the bot for every update received.
 * If you want to use webhook call Bot::processWebhookUpdate() at the end of your bot. The bot will get data from <code>php://input</code> and process it using Bot::processUpdate().
 * Each instance of the bot will open its connection.
 *
 * \subsection Message-commands Message commands
 * Script how the bot will answer to messages containing commands (like <code>/start</code>).
 *
 *     $bot->addMessageCommand("start", function($bot, $message) {
 *             $bot->sendMessage("I am your personal bot, try /help command");
 *     });
 *
 *     $help_function = function($bot, $message) {
 *         $bot->sendMessage("This is the help message")
 *     };
 *
 *     $bot->addMessageCommand("/help", $help_function);
 *
 * Check Bot::addMessageCommand() for more.
 *
 * You can also use regex to check commands.
 *
 * The closure will be called if the commands if the expression evaluates to true. Here is an example:
 *
 *     $bot->addMessageCommandRegex("number\d",
 *         $help_function);
 *
 * The closure will be called when the user send a command that match the regex like, in this example, both <code>/number1</code> or <code>/number135</code>.
 *
 * \subsection Callback-commands Callback commands
 * Script how the bot will answer to callback query containing a particular string as data.
 *
 *     $bot->addCallbackCommand("back", function($bot, $callback_query) {
 *             $bot->editMessageText($callback_query['message']['message_id'], "You pressed back");
 *     });
 *
 * Check Bot::addCallbackCommand() for more.
 *
 * \subsection Bot-Intherited Inherit Bot Class
 * Create a new class that inherits Bot to handle all updates.
 *
 * <code>EchoBot.php</code>
 *
 *     // Create the class that will extends Bot class
 *     class EchoBot extends DanySpin97\PhpBotFramework\Bot {
 *
 *         // Add the function for processing messages
 *         protected function processMessage($message) {
 *
 *             // Answer each message with the text received
 *             $this->sendMessage($message['text']);
 *
 *         }
 *
 *     }
 *
 *     // Create an object of type EchoBot
 *     $bot = new EchoBot("token");
 *
 *     // Process updates using webhook
 *     $bot->processWebhookUpdate();
 *
 * Override these method to make your bot handle each update type:
 * - Bot::processMessage($message)
 * - Bot::processCallbackQuery($callback_query)
 * - Bot::processInlineQuery($inline_query)
 * - Bot::processChosenInlineResult($chosen_inline_result)
 * - Bot::processEditedMessage($edited_message)
 * - Bot::processChannelPost($post)
 * - Bot::processEditedChannelPost($edited_post)
 *
 * \subsection InlineKeyboard-Usage InlineKeyboard Usage
 *
 * How to use the InlineKeyboard class:
 *
 *     // Create the bot
 *     $bot = new DanySpin97\PhpBotFramework\Bot("token");
 *
 *     $command_function = function($bot, $message) {
 *             // Add a button to the inline keyboard
 *             $bot->inline_keyboard->addLevelButtons([
 *                  // with written "Click me!"
 *                  'text' => 'Click me!',
 *                  // and that open the telegram site, if pressed
 *                  'url' => 'telegram.me'
 *                  ]);
 *             // Then send a message, with our keyboard in the parameter $reply_markup of sendMessage
 *             $bot->sendMessage("This is a test message", $bot->inline_keyboard->get());
 *             }
 *
 *     // Add the command
 *     $bot->addMessageCommand("start", $command_function);
 *
 * \subsection Sql-Database Sql Database
 * The sql database is used to save offset from getUpdates and to save user language.
 *
 * To connect a sql database to the bot, a pdo connection is required.
 *
 * Here is a simple pdo connection that is passed to the bot:
 *
 *     $bot->pdo = new PDO('mysql:host=localhost;dbname=test', $user, $pass);
 *
 * \subsection Redis-database Redis Database
 * Redis is used to save offset from getUpdates, to store language (both as cache and persistent) and to save bot state.
 *
 * To connect redis with the bot, create a redis object.
 *
 *     $bot->redis = new Redis();
 *
 * \subsection Multilanguage-section Multilanguage Bot
 * This framework offers method to develop a multi language bot.
 *
 * Here's an example:
 *
 * <code>en.json</code>:
 *
 *     {"Greetings_Msg": "Hello"}
 *
 * <code>it.json</code>:
 *
 *     {"Greetings_Msg": "Ciao"}
 *
 * <code>Greetings.php</code>:
 *
 *     $bot->loadLocalization();
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
 * \section Source
 * The source is hosted on github and can be found [here](https://github.com/DanySpin97/PhpBotFramework).
 *
 * \section Bot-created Bot using this framework
 * - [\@MyAddressBookBot](https://telegram.me/myaddressbookbot) ([Source](https://github.com/DanySpin97/MyAddressBookBot))
 * - [\@Giveaways_bot](https://telegram.me/giveaways_bot) ([Source](https://github.com/DanySpin97/GiveawaysBot))
 *
 * \section Authors
 * This framework is developed and manteined by Danilo Spinella.
 *
 * \section License
 * PhpBotFramework is released under GNU Lesser General Public License.
 * You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the wrapper don't have to be.
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
     * \brief Core of the framework.
     * @{
     */

    /** \brief The bot token (given by @BotFather). */
    private $token;

    /** \brief Url request (containing $token). */
    protected $api_url;

    /** \brief Curl connection for request. */
    public $ch;

    /** \brief Store id of the callback query received. */
    protected $_callback_query_id;

    /** \brief Store id of the inline query received. */
    protected $_inline_query_id;

    /**
     * \brief Contrusct an empty bot.
     * \details Construct a bot passing the token.
     * @param $token Token given by @botfather.
     */
    public function __construct(string $token) {

        // Check token is valid
        if (is_numeric($token) || $token === '') {
            throw new BotException('Token is not valid or empty');
            return;
        }

        // Init variables
        $this->token = $token;
        $this->api_url = 'https://api.telegram.org/bot' . $token . '/';

        // Init connection and config it
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_ENCODING, '');
        // DEBUG
        //curl_setopt($this->ch, CURLOPT_VERBOSE, true);

    }

    /** \brief Destroy the object. */
    public function __destruct() {

        // Close connection
        curl_close($this->ch);

    }

    /** @} */

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /**
     * \brief Get chat id of the current user.
     * @return Chat id of the user.
     */
    public function getChatID() {

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

    /**
     * \brief Get bot ID using getMe API method.
     */
    public function getBotID() : int {

        // Get the id of the bot
        static $bot_id;
        $bot_id = ($this->getMe())['id'];

        // If it is not valid
        if(!isset($bot_id) || $bot_id == 0) {

            // get it again
            $bot_id = ($this->getMe())['id'];

        }

        return $bot_id ?? 0;

    }

    /** @} */

    /**
     * \addtogroup Api Api Methods
     * \brief All api methods to interface the bot with Telegram.
     * @{
     */

    /**
     * \brief A simple method for testing your bot's auth token.
     * \details Requires no parameters. Returns basic information about the bot in form of a User object. [Api reference](https://core.telegram.org/bots/api#getme)
     */
    public function getMe() {

        return $this->exec_curl_request($this->api_url . 'getMe?');

    }


    /**
     * \brief Request bot updates.
     * \details Request updates received by the bot using method getUpdates of Telegram API. [Api reference](https://core.telegram.org/bots/api#getupdates)
     * @param $offset <i>Optional</i>. Identifier of the first update to be returned. Must be greater by one than the highest among the identifiers of previously received updates. By default, updates starting with the earliest unconfirmed update are returned. An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id. The negative offset can be specified to retrieve updates starting from -offset update from the end of the updates queue. All previous updates will forgotten.
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     * @return Array of updates (can be empty).
     */
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 60) {

        $parameters = [
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout,
        ];

        return $this->exec_curl_request($this->api_url . 'getUpdates?' . http_build_query($parameters));

    }

    /**
     * \brief Set updates received by the bot for getUpdates handling.
     * \details List the types of updates you want your bot to receive. For example, specify [“message”, “edited_channel_post”, “callback_query”] to only receive updates of these types. Specify an empty list to receive all updates regardless of type.
     * Set it one time and it won't change until next setUpdateReturned call.
     * @param $allowed_updates <i>Optional</i>. List of updates allowed.
     */
    public function setUpdateReturned(array $allowed_updates = []) {

        // Parameter for getUpdates
        $parameters = [
            'offset' => 0,
            'limit' => 1,
            'timeout' => 0,
        ];

        // Start the list
        $updates_string = '[';

        // Flag to skip adding ", " to the string
        $first_string = true;

        // Iterate over the list
        foreach ($allowed_updates as $index => $update) {

            // Is it the first update added?
            if (!$first_string) {

                $updates_string .= ', "' . $update . '"';

            } else {

                $updates_string .= '"' . $update . '"';

                // Set the flag to false cause we added an item
                $first_string = false;

            }

        }

        // Close string with the marker
        $updates_string .= ']';

        // Exec getUpdates
        $this->exec_curl_request($this->api_url . 'getUpdates?' . http_build_query($parameters) . '&allowed_updates=' . $updates_string);

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
    public function sendMessage($text, string $reply_markup = null, int $reply_to = null, string $parse_mode = 'HTML', bool $disable_web_preview = true, bool $disable_notification = false) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'text' => $text,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
            'reply_markup' => $reply_markup,
            'reply_to_message_id' => $reply_to,
            'disable_notification' => $disable_notification
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
    public function forwardMessage($from_chat_id, int $message_id, bool $disable_notification = false) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'message_id' => $message_id,
            'from_chat_id' => $from_chat_id,
            'disable_notification' => $disable_notification
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
    public function sendPhoto($photo, string $reply_markup = null, string $caption = '', bool $disable_notification = false) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->exec_curl_request($this->api_url . 'sendPhoto?' . http_build_query($parameters));

    }

    /**
     * \brief Send an audio.
     * \details Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .mp3 format. [Api reference](https://core.telegram.org/bots/api/#sendaudio)
     * Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
     * @param $audio Audio file to send. Pass a file_id as String to send an audio file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get an audio file from the Internet, or upload a new one using multipart/form-data.
     * @param $caption <i>Optional</i>. Audio caption, 0-200 characters.
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param $duration <i>Optional</i>. Duration of the audio in seconds.
     * @param $performer <i>Optional</i>. Performer.
     * @param $title <i>Optional</i>. Track name.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @param $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return On success, the sent message.
     */
    public function sendAudio($audio, string $caption = null, string $reply_markup = null, int $duration = null, string $title = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'audio' => $photo,
            'caption' => $caption,
            'duration' => $duration,
            'performer' => $performer,
            'title' => $title,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->exec_curl_request($this->api_url . 'sendAudio?' . http_build_query($parameters));

    }

    /**
     * \brief Send a document.
     * \details Use this method to send general files. [Api reference](https://core.telegram.org/bots/api/#senddocument)
     * @param $document File to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data.
     * @param <i>Optional</i>. Document caption (may also be used when resending documents by file_id), 0-200 characters.
     *
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param <i>Optional</i>. Sends the message silently.
     * @param <i>Optional</i>. If the message is a reply, ID of the original message.
     */
    public function sendDocument($document, string $caption = '', string $reply_markup = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'document' => $photo,
            'caption' => $caption,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->exec_curl_request($this->api_url . 'sendAudio?' . http_build_query($parameters));

    }


    /**
     * \brief Send a sticker
     * \details Use this method to send .webp stickers. [Api reference](https://core.telegram.org/bots/api/#sendsticker)
     * @param $sticker Sticker to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a .webp file from the Internet, or upload a new one using multipart/form-data.
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param $disable_notification Sends the message silently.
     * @param <i>Optional</i>. If the message is a reply, ID of the original message.
     * @param On success, the sent message.
     */
    public function sendSticker($sticker, string $reply_markup = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'sticker' => $sticker,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ];

        return $this->exec_curl_request($this->api_url . 'sendSticker?' . http_build_query($parameters));

    }

    /**
     * \brief Send audio files.
     * \details Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message. For this to work, your audio must be in an .ogg file encoded with OPUS (other formats may be sent as Audio or Document).o
     * Bots can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
     * @param $voice Audio file to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data.
     * @param $caption <i>Optional</i>. Voice message caption, 0-200 characters
     * @param $duration <i>Optional</i>. Duration of the voice message in seconds
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @param $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return On success, the sent message is returned.
     */
    public function sendVoice($voice, string $caption, int $duration, string $reply_markup = null, bool $disable_notification, int $reply_to_message_id = 0) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'voice' => $voice,
            'caption' => $caption,
            'duration' => $duration,
            'disable_notification', $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ];

        return $this->exec_curl_request($this->api_url . 'sendVoice?' . http_build_query($parameters));

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
    public function sendChatAction(string $action) : bool {

        $parameters = [
            'chat_id' => $this->chat_id,
            'action' => $action
        ];

        return $this->exec_curl_request($this->api_url . 'sendChatAction?' . http_build_query($parameters));

    }

    /**
     * \brief Get info about a chat.
     * \details Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.). [Api reference](https://core.telegram.org/bots/api#getchat)
     * @param Unique identifier for the target chat or username of the target supergroup or channel (in the format <code>@channelusername</code>)
     */
    public function getChat($chat_id) {

        $parameters = [
            'chat_id' => $chat_id,
        ];

        return $this->exec_curl_request($this->api_url . 'getChat?' . http_build_query($parameters));

    }

    /**
     * \brief Use this method to get a list of administrators in a chat.
     * @param Unique identifier for the target chat or username of the target supergroup or channel (in the format <code>@channelusername</code>)
     * @return On success, returns an Array of ChatMember objects that contains information about all chat administrators except other bots. If the chat is a group or a supergroup and no administrators were appointed, only the creator will be returned.
     */
    public function getChatAdministrators($chat_id) {

        $parameters = [
            'chat_id' => $chat_id,
        ];

        return $this->exec_curl_request($this->api_url . 'getChatAdministrators?' . http_build_query($parameters));

    }


    /* \brief Answer a callback query
     * \details Remove the updating cirle on an inline keyboard button and showing a message/alert to the user.
     * It will always answer the current callback query.
     * @param $text <i>Optional</i>. Text of the notification. If not specified, nothing will be shown to the user, 0-200 characters.
     * @param $show_alert <i>Optional</i>. If true, an alert will be shown by the client instead of a notification at the top of the chat screen.
     * @param $url <i>Optional</i>. URL that will be opened by the user's client. If you have created a Game and accepted the conditions via @Botfather, specify the URL that opens your game – note that this will only work if the query comes from a callback_game button.
     * Otherwise, you may use links like telegram.me/your_bot?start=XXXX that open your bot with a parameter.
     * @return True on success.
     */
    public function answerCallbackQuery($text = '', $show_alert = false, string $url = '') : bool {

        if (!isset($this->_callback_query_id)) {

            throw new BotException("Callback query id not set, wrong update");

        }

        $parameters = [
            'callback_query_id' => $this->_callback_query_id,
            'text' => $text,
            'show_alert' => $show_alert,
            'url' => $url
        ];

        return $this->exec_curl_request($this->api_url . 'answerCallbackQuery?' . http_build_query($parameters));

    }

    /**
     * \brief Edit text of a message sent by the bot.
     * \details Use this method to edit text and game messages sent by the bot. [Api reference](https://core.telegram.org/bots/api#editmessagetext)
     * @param $message_id Unique identifier of the sent message.
     * @param $text New text of the message.
     * @param $reply_markup Reply markup of the message will have (will be removed if this is null).
     * @param $parse_mode <i>Optional</i>. Send Markdown or HTML.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     */
    public function editMessageText(int $message_id, $text, $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = true) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
        ];

        return $this->exec_curl_request($this->api_url . 'editMessageText?' . http_build_query($parameters));

    }

    /**
     * \brief Edit text of a message sent via the bot.
     * \details Use this method to edit text messages sent via the bot (for inline queries). [Api reference](https://core.telegram.org/bots/api#editmessagetext)
     * @param $inline_message_id  Identifier of the inline message.
     * @param $text New text of the message.
     * @param $reply_markup Reply markup of the message will have (will be removed if this is null).
     * @param $parse_mode <i>Optional</i>. Send Markdown or HTML.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     */
    public function editInlineMessageText(string $inline_message_id, $text, string $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = false) {

        $parameters = [
           'inline_message_id' => $inline_message_id,
           'text' => $text,
           'reply_markup' => $inline_keyboard,
           'parse_mode' => $parse_mode,
           'disable_web_page_preview' => $disable_web_preview,
        ];

        return $this->exec_curl_request($this->api_url . 'editMessageText?' . http_build_query($parameters));

    }

    /*
     * Edit only the inline keyboard of a message (https://core.telegram.org/bots/api#editmessagereplymarkup)ù
     * @param
     * $message_id Identifier of the message to edit
     * $inline_keyboard Inlike keyboard array (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     */
    public function editMessageReplyMarkup($message_id, $inline_keyboard) {

        $parameters = [
            'chat_id' => $this->chat_id,
            'message_id' => $message_id,
            'reply_markup' => $inline_keyboard,
        ];

        return $this->exec_curl_request($this->api_url . 'editMessageReplyMarkup?' . http_build_query($parameters));

    }

    /*
     * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
     * @param
     * $results Array on InlineQueryResult (https://core.telegram.org/bots/api#inlinequeryresult)
     * $switch_pm_text Text to show on the button
     */
    public function answerInlineQuerySwitchPM($results, $switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {

        if (!isset($this->_inline_query_id)) {

            throw new BotException("Inline query id not set, wrong update");

        }

        $parameters = [
            'inline_query_id' => $this->_inline_query_id,
            'switch_pm_text' => $switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'results' => $results,
            'cache_time' => $cache_time
        ];

        return $this->exec_curl_request($this->api_url . 'answerInlineQuery?' . http_build_query($parameters));

    }

    /*
     * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
     * without showing any results to the user
     * @param
     * $switch_pm_text Text to show on the button
     */
    public function answerEmptyInlineQuerySwitchPM($switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {

        if (!isset($this->_inline_query_id)) {

            throw new BotException("Inline query id not set, wrong update");

        }

        $parameters = [
            'inline_query_id' => $this->_inline_query_id,
            'switch_pm_text' => $switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'cache_time' => $cache_time
        ];

        return $this->exec_curl_request($this->api_url . 'answerInlineQuery?' . http_build_query($parameters));

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
    public function apiRequest(string $method, array $parameters) {

        return $this->exec_curl_request($this->api_url . $method . '?' . http_build_query($parameters));

    }

    /** @} */

    /**
     * \addtogroup Core Core(internal)
     * @{
     */

    /** \brief Core function to execute url request.
     * @param $url The url to call using the curl session.
     * @return Url response, false on error.
     */
    protected function exec_curl_request($url) {

        // Set the url
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

