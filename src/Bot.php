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

use PhpBotFramework\Entities\InlineKeyboard;

class Bot extends Core\BaseBot
{

    use Commands\MessageCommand,
        Commands\MessageRegexCommand,
        Commands\CallbackCommand,
        Database\LongPolling,
        Database\Handler,
        Database\User,
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
     * @param string $token Bot token given by BotFather.
     */
    public function __construct(string $token)
    {

        // Parent constructor
        parent::__construct($token);

        // Initialize to an empty array
        $this->_message_commands = [];
        $this->_callback_commands = [];

        $this->keyboard = new InlineKeyboard($this);
    }

    /** \brief Descruct the bot. */
    public function __destruct()
    {

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
}
