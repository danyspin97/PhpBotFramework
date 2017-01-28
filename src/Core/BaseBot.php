<?php

namespace PhpBotFramework\Core;

use PhpBotFramework\Exceptions\BotException;

/**
 * \class Bot Bot
 * \brief Bot class to handle updates and commandes.
 * \details Class Bot to handle task like api request, or more specific api function(sendMessage, editMessageText, etc).
 * Usage example in webhook.php
 *
 */
class BaseBot extends CoreBot {

    use \PhpBotFramework\Commands\CommandHandler;

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    /** \brief Is the bot using webhook? */
    protected $_is_webhook;

    /**
     * \brief Construct an empty base bot.
     * \details Construct a base bot that can handle updates.
     */
    public function __construct(string $token) {

        // Parent constructor
        parent::__construct($token);

        // Add alias for entity classes
        class_alias('PhpBotFramework\Entities\Message', 'Message');
        class_alias('PhpBotFramework\Entities\CallbackQuery', 'CallbackQuery');
        class_alias('PhpBotFramework\Entities\InlineQuery', 'InlineQuery');
        class_alias('PhpBotFramework\Entities\ChosenInlineResult', 'ChosenInlineResult');
        class_alias('Message', 'EditedMessage');
        class_alias('Message', 'ChannelPost');
        class_alias('Message', 'EditedChannelPost');

    }

    /** @} */

    /**
     * \addtogroup Bot
     * @{
     */

    /**
     * \brief Get update and process it.
     * \details Call this method if you are using webhook.
     * It will get update from php::\input, check it and then process it using processUpdate.
     */
    public function processWebhookUpdate() {

        $this->_is_webhook = true;

        $this->initCommands();

        $this->processUpdate(json_decode(file_get_contents('php://input'), true));

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

        $this->initCommands();

        // Process all updates
        while (true) {

            // Set parameter for the url call
            $parameters = [
                'offset' => $offset,
                'limit' => $limit,
                'timeout' => $timeout
            ];

            $updates = $this->exec_curl_request($this->_api_url . 'getUpdates?' . http_build_query($parameters));

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

    /** @} */

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    /**
     * \brief Dispatch each update to the right method (processMessage, processCallbackQuery, etc).
     * \details Set $chat_id for each update, $text, $data and $query are set for each update that contains them.
     * @param $update Reference to the update received.
     * @return The id of the update processed.
     */
    protected function processUpdate(array $update) : int {

        static $updates_type = ['message' => 'Message',
            'callback_query' => 'CallbackQuery',
            'inline_query' => 'InlineQuery',
            'channel_post' => 'ChannelPost',
            'edited_message' => 'EditedMessage',
            'edited_channel_post' => 'EditedChannelPost',
            'chosen_inline_result' => 'ChosenInlineResult'];

        foreach ($updates_type as $offset => $class) {

            if (isset($update[$offset])) {

                $object = new $class($update[$offset]);

                $this->_chat_id = $object->getChatID();

                if (method_exists($object, 'getBotParameter')) {

                    $var = $object->getBotParameter();
                    $this->{$var['var']} = $var['id'];

                }

                $this->{"process$class"}($object);

                return $update['update_id'];

            }

        }

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
