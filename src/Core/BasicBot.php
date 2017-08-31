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

namespace PhpBotFramework;

use PhpBotFramework\Exceptions\BotException;
use PhpBotFramework\Entities\Message;
use PhpBotFramework\Entities\CallbackQuery;
use PhpBotFramework\Entities\ChosenInlineResult;
use PhpBotFramework\Entities\InlineQuery;
use \PhpBotFramework\Commands\CommandHandler;

/**
 * \class Bot Bot
 * \brief Bot class to handle updates and commands.
 * \details Class Bot to handle task like API request, or more specific API method like sendMessage, editMessageText, etc..
 * An example of its usage is available in webhook.php
 *
 */
class BasicBot extends Core\CoreBot
{
    use CommandHandler,
        Config;

    /** @internal
      * \brief True if the bot is using webhook? */
    protected $_is_webhook = false;

    public $answerUpdate;

    public static $update_types = [
            'message' => 'Message',
            'callback_query' => 'CallbackQuery',
            'inline_query' => 'InlineQuery',
            'channel_post' => 'ChannelPost',
            'edited_message' => 'EditedMessage',
            'edited_channel_post' => 'EditedChannelPost',
            'chosen_inline_result' => 'ChosenInlineResult',
            'pre_checkout_query' => 'PreCheckoutQuery',
            'shipping_query' => 'ShippingQuery'
        ];

    /**
     * \brief Construct an empty base bot.
     * \details Construct a base bot that can handle updates.
     */
    public function __construct(string $token)
    {
        parent::__construct($token);

        $this->answerUpdate = [];

        // Init all default fallback for updates
        foreach (BasicBot::$update_types as $type => $classes) {
            $this->answerUpdate[$type] = function ($bot, $message) {
            };
        }

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
     * It'll get bot's update from php::\input, check it and then process it using <b>processUpdate</b>.
     */
    public function processWebhookUpdate()
    {
        $this->_is_webhook = true;

        $this->init();
        $this->processUpdate(json_decode(file_get_contents('php://input'), true));
    }

    /**
     * \brief Get updates received by the bot, and hold the offset in $offset.
     * \details Get the <code>update_id</code> of the first update to parse, set it in $offset and
     * then it start an infinite loop where it processes updates and keep $offset on the update_id of the last update received.
     * Each processUpdate() method call is surrounded by a try/catch.
     * @see getUpdates
     * @param int $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param int $timeout <i>Optional</i>. Timeout in seconds for long polling.
     */
    public function getUpdatesLocal(int $limit = 100, int $timeout = 60)
    {
        $update = [];

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
     * @internal
     * \brief Dispatch each update to the right method (processMessage, processCallbackQuery, etc).
     * \details Set $chat_id for each update, $text, $data and $query are set for each update that contains them.
     * @param array $update Reference to the update received.
     * @return int The id of the update processed.
     */
    protected function processUpdate(array $update) : int
    {
        if ($this->processCommands($update)) {
            return $update['update_id'];
        }

        // For each update type
        foreach (BasicBot::$update_types as $offset => $class) {
            // Did we receive this type of the update?
            if (isset($update[$offset])) {
                $object_class = "PhpBotFramework\Entities\\$class";
                $object = new $object_class($update[$offset]);

                $this->chat_id = $object->getChatID();

                $this->setAdditionalData($object);

                $this->answerUpdate[$offset]($this, $object);

                return $update['update_id'];
            }
        }
    }

    protected function setAdditionalData($entity)
    {
        if (method_exists($entity, 'getBotParameter')) {
            $var = $entity->getBotParameter();
            $this->{$var['var']} = $var['id'];
        }
    }

    /**
     * \brief Set compatibilityu mode for old processes method.
     * \details If your bot uses `processMessage` or another deprecated function, call this method to make the old version works.
     */
    public function oldDispatch()
    {
        // For each update type
        foreach (BasicBot::$update_types as $offset => $class) {
            // Check if the bot has an inherited method
            if (method_exists($this, 'process' . $class)) {
                // Wrap it in a closure to make it works with the 3.0 version
                $this->answerUpdate[$offset] = function ($bot, $entity) use ($class) {
                    $bot->{"process$class"}($entity);
                };
            }
        }
    }

    public function init()
    {
        $this->initCommands();
        if ($this->_is_webhook) {
            $this->logger->pushHandler(new StreamHandler('/var/log/' . $this->bot_name . '.log', Logger::WARNING));
        } else {
            if ($this->getBotID === 0) {
                throw BotException("The bot could not be started");
            }
            $logger_path = $this->getScriptPath() . $this->bot_name . '.log';
            $this->logger->pushHandler(new StreamHandler($logger_path, Logger::WARNING));
            print('The bot has been started successfully.
                A log file has been created at ' . $logger_path .
                '\nTo stop it press <C-c> (Control-C).');
        }
    }

    /** @} */
}
