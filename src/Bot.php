<?php

namespace PhpBotFramework;

use PhpBotFramework\Exceptions\BotException;

use PhpBotFramework\Entities\InlineKeyboard;

class Bot extends Core\BaseBot {

    use Commands\MessageCommand,
        Commands\MessageRegexCommand,
        Commands\CallbackCommand,
        Database\LongPolling,
        Database\DatabaseHandler,
        Localization\File,
        Localization\Language,
        Localization\LocalizatedString,
        Utilities\BotState;

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

    /** @} */

    /** @} */


}
