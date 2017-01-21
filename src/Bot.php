<?php

namespace PhpBotFramework;

use PhpBotFramework\Exceptions\BotException;

use PhpBotFramework\Entities\InlineKeyboard;

class Bot extends Core\BaseBot {

    use Commands\CommandHandler,
        Commands\MessageCommand,
        Commands\MessageRegexCommand,
        Commands\CallbackCommand,
        Database\LongPolling,
        Database\DatabaseHandler,
        Utilities\BotState,
        Utilities\Localization;

    /**
     * \addtogroup Bot Bot
     * \brief Properties and methods to handle the TelegramBot.
     * \details Here are listed all the properties and methods that will help the developer create the basic bot functions.
     * @{
     */

    /** \brief Store the inline keyboard */
    public $keyboard;

    /** \brief Pdo reference */
    public $pdo;

    /** \brief Redis connection */
    public $redis;

    /**
     * \brief Construct an empty bot.
     * \details Construct a bot that can handle updates, localization, database connection and handling, redis database.
     */
    public function __construct(string $token) {

        // Parent constructor
        parent::__construct($token);

        // Initialize to an empty array
        $this->_message_commands = [];
        $this->_callback_commands = [];

        $this->keyboard = new InlineKeyboard($this);

    }

    /** \brief Descruct the bot. */
    public function __destruct() {

        // Close redis connection if it is open
        if (isset($this->redis)) {

            $this->redis->close();

        }

        // Close database connection if it is open
        if (isset($this->pdo)) {

            $this->pdo = null;

        }

    }

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

}
