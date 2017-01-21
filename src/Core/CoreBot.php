<?php

namespace PhpBotFramework\Core;

use \PhpBotFramework\Exceptions\BotException;

use \PhpBotFramework\Entities\InlineKeyboard;

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
 * Each api call will have <code>$_chat_id</code> set to the current user, use CoreBot::setChatID() to change it.
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
 * - Bot::processChosenInlineResult($_chosen_inline_result)
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

    use Updates,
        Send,
        Edit,
        Inline,
        Chat;

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /** \brief Chat_id of the user that interacted with the bot */
    protected $_chat_id;

    /** @} */

    /**
     * \addtogroup Core Core(Internal)
     * \brief Core of the framework.
     * @{
     */

    /** \brief The bot token (given by @BotFather). */
    private $token;

    /** \brief Url request (containing $token). */
    protected $_api_url;

    /** \brief Implements interface for execute HTTP requests. */
    protected $_http;

    /**
     * \brief Contrusct an empty bot.
     * \details Construct a bot passing the token.
     * @param $token Token given by @botfather.
     */
    public function __construct(string $token) {

        // Check token is valid
        if (is_numeric($token) || $token === '') {

            throw new BotException('Token is not valid or empty');

        }

        // Init variables
        $this->token = $token;
        $this->_api_url = 'https://api.telegram.org/bot' . $token . '/';

        // Init connection and config it
        $this->_http = new \GuzzleHttp\Client([
            'base_uri' => $this->_api_url,
            'connect_timeout' => 5,
            'verify' => false,
            'timeout' => 60,
            'http_errors' => false
        ]);

        return;
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

        return $this->_chat_id;

    }

    /**
     * \brief Set current chat id.
     * \details Change the chat id which the bot execute api methods.
     * @param $_chat_id The new chat id to set.
     */
    public function setChatID($_chat_id) {

        $this->_chat_id = $_chat_id;

    }

    /**
     * \brief Get bot ID using getMe API method.
     */
    public function getBotID() : int {

        // Get the id of the bot
        static $bot_id;
        $bot_id = ($this->getMe())['id'];

        // If it is not valid
        if (!isset($bot_id) || $bot_id == 0) {

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
     * \brief Exec any api request using this method.
     * \details Use this method for custom api calls using this syntax:
     *
     *     $param = [
     *             'chat_id' => $_chat_id,
     *             'text' => 'Hello!'
     *     ];
     *     apiRequest("sendMessage", $param);
     *
     * @param $method The method to call.
     * @param $parameters Parameters to add.
     * @return Depends on api method.
     */
    public function apiRequest(string $method, array $parameters) {

        return $this->exec_curl_request($method . '?' . http_build_query($parameters));

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
    protected function exec_curl_request($url, $method = 'POST') {

        $response = $this->_http->request($method, $url);
        $http_code = $response->getStatusCode();

        if ($http_code === 200) {
            $response = json_decode($response->getBody(), true);

            if (isset($response['desc'])) {
                error_log("Request was successfull: {$response['description']}\n");
            }

            return $response['result'];
        } elseif ($http_code >= 500) {
            // do not wat to DDOS server if something goes wrong
            sleep(10);
            return false;
        } else {
            $response = json_decode($response->getBody(), true);
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

