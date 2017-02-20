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

// Use localized inline keyboard: this means you can display it in various languages.
use PhpBotFramework\Localization\Button;

/** \class Bot Bot class that contains all modules.
 */
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
     * \brief Properties and methods to handle your Telegrams bot.
     * \details Here're listed all the properties and methods that offers facilities for bot's basic features.
     * @{
     */

    /** \brief Store the inline keyboard */
    public $keyboard;

    /** \brief Manage connection with database using PDO */
    public $pdo;

    /** \brief Manage connection with Redis */
    public $redis;

    /**
     * \brief Construct an empty bot.
     * \details Construct a complete Telegram bot which can use localization, database and more other.
     *
     * @param string $token Bot token, you can request one through **BotFather** on Telegram.
     */
    public function __construct(string $token)
    {
        parent::__construct($token);

        $this->_message_commands = [];
        $this->_callback_commands = [];

        $this->keyboard = new Button($this);
    }

    /** \brief Destroy the bot closing connections with database and Redis */
    public function __destruct()
    {
        if (isset($this->redis)) {
            $this->redis->close();
        }

        if (isset($this->pdo)) {
            $this->pdo = null;
        }
    }

    /** @} */
}
