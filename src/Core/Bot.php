<?php

namespace PhpBotFramework;

use Exceptions\BotException;

use Entities\InlineKeyboard;

/**
 * \class Bot Bot
 * \brief Bot class to handle updates and commandes.
 * \details Class Bot to handle task like api request, or more specific api function(sendMessage, editMessageText, etc).
 * Usage example in webhook.php
 *
 */
class Bot extends Core\CoreBot {

    use LongPolling,
        MessageCommand,
        CallbackCommand,
        DatabaseHandler,
        BotState,
        Localization;

    /**
     * \addtogroup Bot Bot
     * \brief Properties and methods to handle the TelegramBot.
     * \details Here are listed all the properties and methods that will help the developer create the basic bot functions.
     * @{
     */

    /** \brief Text received in messages */
    protected $_text;

    /** \brief Data received in callback query */
    protected $_data;

    /** \brief Query sent by the user in the inline query */
    protected $_query;

    /** \brief Store the inline keyboard */
    public $keyboard;

    /** \brief Pdo reference */
    public $pdo;

    /** \brief Redis connection */
    public $redis;

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
        $this->_message_commands = [];
        $this->_callback_commands = [];

        $this->keyboard = new InlineKeyboard($this);

    }

    /** \brief Descruct the class. */
    public function __destruct() {
        // Close redis connection if it is open
        if (isset($this->redis)) {

            $this->redis->close();

        }

    }

    /**
     * \brief Get the text of the message, if set (for updates of type "message").
     * @return Text of the message, empty string if not set.
     */
    public function getMessageText() : string {

        if (isset($this->_text)) {

            return $this->_text;

        }

        return '';

    }

    /**
     * \brief Get the data of callback query, if set (for updates of type "callback_query").
     * @return Data of the callback query, empty string if not set.
     */
    public function getCallbackData() : string {

        if (isset($this->_data)) {

            return $this->_data;

        }

        return '';

    }

    /**
     * \brief Get the query received from the inline query (for updates of type "inline_query").
     * @return The query sent by the user, throw exception if the current update is not an inline query.
     */
    public function getInlineQuery() : string {

        if (isset($this->_query)) {

            return $this->_query;

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
        $this->_message_commands_set = !empty($this->_message_commands);

        // Are there callback commands?
        $this->_callback_commands_set = !empty($this->_callback_commands);

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
            $this->_chat_id = $update['message']['chat']['id'];

            // If the message contains text
            if (isset($update['message']['text'])) {

                $this->_text = $update['message']['text'];

            }

            // If there are commands set by the user
            // and there are bot commands in the message, checking message entities
            if ($this->_message_commands_set && isset($update['message']['entities']) && $update['message']['entities'][0]['type'] === 'bot_command') {

                // The lenght of the command
                $length = $update['message']['entities'][0]['length'];

                // Offset of the command
                $offset = $update['message']['entities'][0]['offset'];

                // For each command added by the user
                foreach ($this->_message_commands as $trigger) {

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
                        unset($this->_text);

                        // and return the id of the current update to stop processing this update
                        return $update['update_id'];

                    }

                }

            }

            // And process it
            $this->processMessage($update['message']);

            // clear text variable
            unset($this->_text);

            // If the update is a callback query
        } elseif (isset($update['callback_query'])) {

            // Set variables
            $this->_chat_id = $update['callback_query']['message']['chat']['id'];
            $this->_callback_query_id = $update['callback_query']['id'];

            // If data is set for the current callback query
            if (isset($update['callback_query']['data'])) {

                $this->_data = $update['callback_query']['data'];

            }

            // Check for callback commands
            if (isset($this->_data) && $this->_callback_commands_set) {

                // Parse all commands
                foreach ($this->_callback_commands as $trigger) {

                    // If command is found in callback data
                    if (strpos($trigger['data'], $this->_data) !== false) {

                        // Trigger the script
                        $trigger['script']($this, $update['callback_query']);

                        // Clear data
                        unset($this->_data);
                        unset($this->_callback_query_id);

                        // and return the id of the current update
                        return $update['update_id'];

                    }

                }

            }

            // Process the callback query through processCallbackQuery
            $this->processCallbackQuery($update['callback_query']);

            // Unset callback query variables
            unset($this->_callback_query_id);
            unset($this->_data);

        } elseif (isset($update['inline_query'])) {

            $this->_chat_id = $update['inline_query']['from']['id'];
            $this->_query = $update['inline_query']['query'];
            $this->_inline_query_id = $update['inline_query']['id'];

            $this->processInlineQuery($update['inline_query']);

            unset($this->_query);
            unset($this->_inline_query_id);

        } elseif (isset($update['channel_post'])) {

            // Set data from the post
            $this->_chat_id = $update['channel_post']['chat']['id'];

            $this->processChannelPost($update['channel_post']);

        } elseif (isset($update['edited_message'])) {

            $this->_chat_id = $update['edited_message']['chat']['id'];

            $this->processEditedMessage($update['edited_message']);

        } elseif (isset($update['edited_channel_post'])) {

            $this->_chat_id = $update['edited_channel_post']['chat']['id'];

            $this->processEditedChannelPost($update['edited_channel_post']);

        } elseif (isset($update['chosen_inline_result'])) {

            $this->_chat_id = $update['chosen_inline_result']['chat']['id'];

            $this->processChosenInlineResult($update['chosen_inline_result']);

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
     * <code>$chat_id</code> and <code>$text</code>, if the message contains text(use getMessageText() to access it), set inside of this function.
     * @param $message Reference to the message received.
     */
    protected function processMessage($message) {

    }

    /**
     * \brief Called every callback query received by the bot.
     * \details Override it to script the bot answer for each callback.
     * <code>$chat_id</code> and <code>$data</code>, if set in the callback query(use getCallbackData() to access it) set inside of this function.
     * @param $callback_query Reference to the callback query received.
     */
    protected function processCallbackQuery($callback_query) {

    }

    /**
     * \brief Called every inline query received by the bot.
     * \details Override it to script the bot answer for each inline query.
     * $chat_id and $query(use getInlineQuery() to access it) set inside of this function.
     * @param $inline_query Reference to the inline query received.
     */
    protected function processInlineQuery($inline_query) {

    }

    /**
     * \brief Called every chosen inline result received by the bot.
     * \details Override it to script the bot answer for each chosen inline result.
     * <code>$chat_id</code> set inside of this function.
     * @param $chosen_inline_result Reference to the chosen inline result received.
     */
    protected function processChosenInlineResult($chosen_inline_result) {

    }

    /**
     * \brief Called every chosen edited message received by the bot.
     * \details Override it to script the bot answer for each edited message.
     * <code>$chat_id</code> set inside of this function.
     * @param $edited_message The message edited by the user.
     */
    protected function processEditedMessage($edited_message) {

    }

    /**
     * \brief Called every new post in the channel where the bot is in.
     * \details Override it to script the bot answer for each post sent in a channel.
     * <code>$chat_id</code> set inside of this function.
     * @param $post The message sent in the channel.
     */
    protected function processChannelPost($post) {

    }

    /**
     * \brief Called every time a post get edited in the channel where the bot is in.
     * \details Override it to script the bot answer for each post edited  in a channel.
     * <code>$chat_id</code> set inside of this function.
     * @param $post The message edited in the channel.
     */
    protected function processEditedChannelPost($edited_post) {

    }

    /** @} */

}
