<?php

/*
 * This file is part of the PhpBotFramework.
 *
 * PhpBotFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * PhpBotFramework is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhpBotFramework\Core;

use PhpBotFramework\Exceptions\BotException;

use PhpBotFramework\Entities\Message;
use PhpBotFramework\Entities\CallbackQuery;
use PhpBotFramework\Entities\ChosenInlineResult;
use PhpBotFramework\Entities\InlineQuery;

/**
 * \class Bot Bot
 * \brief Bot class to handle updates and commandes.
 * \details Class Bot to handle task like API request, or more specific API method like sendMessage, editMessageText, etc..
 * An example of its usage is available in webhook.php
 *
 */
class BaseBot extends CoreBot
{
    use \PhpBotFramework\Commands\CommandHandler;

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    protected $_is_webhook;

    /**
     * \brief Construct an empty base bot.
     * \details Construct a base bot that can handle updates.
     */
    public function __construct(string $token)
    {
        parent::__construct($token);

        // Add alias for entity classes
        class_alias('PhpBotFramework\Entities\Message', 'PhpBotFramework\Entities\EditedMessage');
        class_alias('PhpBotFramework\Entities\Message', 'PhpBotFramework\Entities\ChannelPost');
        class_alias('PhpBotFramework\Entities\Message', 'PhpBotFramework\Entities\EditedChannelPost');
    }

    /** @} */

    /**
     * \addtogroup Bot
     * @{
     */

    /**
     * \brief Get update and process it.
     * \details Call this method if user is using webhook.
     * It'll get bot's update from php::\input, check it and then process it using processUpdate.
     */
    public function processWebhookUpdate()
    {
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
     * @param int $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param int $timeout <i>Optional</i>. Timeout in seconds for long polling.
     */
    public function getUpdatesLocal(int $limit = 100, int $timeout = 60)
    {
        // While there aren't updates to process
        while (empty($update = $this->getUpdates(0, 1))) {
        }

        $offset = $update[0]['update_id'];
        $this->initCommands();

        // Process all updates
        while (true) {
            $updates = $this->execRequest("getUpdates?offset=$offset&limit=$limit&timeout=$timeout");

            foreach ($updates as $key => $update) {
                try {
                    // Process one at a time
                    $this->processUpdate($update);
                } catch (BotException $e) {
                    echo $e->getMessage();
                }
            }

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
     * @param array $update Reference to the update received.
     * @return int The id of the update processed.
     */
    protected function processUpdate(array $update) : int
    {
        static $updates_type = ['message' => 'Message',
            'callback_query' => 'CallbackQuery',
            'inline_query' => 'InlineQuery',
            'channel_post' => 'ChannelPost',
            'edited_message' => 'EditedMessage',
            'edited_channel_post' => 'EditedChannelPost',
            'chosen_inline_result' => 'ChosenInlineResult'];

        foreach ($updates_type as $offset => $class) {
            if (isset($update[$offset])) {
                $object_class = "PhpBotFramework\Entities\\$class";
                $object = new $object_class($update[$offset]);

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
     * @param Message $message Reference to the message received.
     */
    protected function processMessage(Message $message)
    {
    }

    /**
     * \brief Called every callback query received by the bot.
     * \details Override it to script the bot answer for each callback.
     * <code>$chat_id</code> and <code>$data</code>, if set in the callback query(use getCallbackData() to access it) set inside of this function.
     * @param CallbackQuery $callback_query Reference to the callback query received.
     */
    protected function processCallbackQuery(CallbackQuery $callback_query)
    {
    }

    /**
     * \brief Called every inline query received by the bot.
     * \details Override it to script the bot answer for each inline query.
     * $chat_id and $query(use getInlineQuery() to access it) set inside of this function.
     * @param InlineQuery $inline_query Reference to the inline query received.
     */
    protected function processInlineQuery(InlineQuery $inline_query)
    {
    }

    /**
     * \brief Called every chosen inline result received by the bot.
     * \details Override it to script the bot answer for each chosen inline result.
     * <code>$chat_id</code> set inside of this function.
     * @param ChosenInlineResult $chosen_inline_result Reference to the chosen inline result received.
     */
    protected function processChosenInlineResult(ChosenInlineResult $chosen_inline_result)
    {
    }

    /**
     * \brief Called every chosen edited message received by the bot.
     * \details Override it to script the bot answer for each edited message.
     * <code>$chat_id</code> set inside of this function.
     * @param Message $edited_message The message edited by the user.
     */
    protected function processEditedMessage(Message $edited_message)
    {
    }

    /**
     * \brief Called every new post in the channel where the bot is in.
     * \details Override it to script the bot answer for each post sent in a channel.
     * <code>$chat_id</code> set inside of this function.
     * @param Message $post The message sent in the channel.
     */
    protected function processChannelPost(Message $post)
    {
    }

    /**
     * \brief Called every time a post get edited in the channel where the bot is in.
     * \details Override it to script the bot answer for each post edited  in a channel.
     * <code>$chat_id</code> set inside of this function.
     * @param Message $post The message edited in the channel.
     */
    protected function processEditedChannelPost(Message $edited_post)
    {
    }

    /** @} */
}
