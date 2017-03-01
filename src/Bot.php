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

// Use inline keyboard with localizated buttons
use PhpBotFramework\Localization\Button;

use PhpBotFramework\Utilities\BotState;

use PhpBotFramework\Database\Database;

use PhpBotFramework\Database\Getter;
use PhpBotFramework\Database\LongPolling;

use PhpBotFramework\Localization\Localization;

/** \class Bot Bot class that contains all modules.
 */
class Bot extends BasicBot
{
    use Getter;

    /**
     * \addtogroup Bot Bot
     * \brief Properties and methods to handle your Telegram's bot.
     * \details Here are listed all the properties and methods that will help
     * the developers during creation of basic bot's features.
     * @{
     */

    /** \brief Store the inline keyboard. */
    public $keyboard;

    /** \brief Database handling object. */
    public $database;

    /** \brief Redis connection. */
    public $redis;

    public $local;

    /**
     * \brief Construct an empty bot.
     * \details Construct a bot that can handle updates, localization, database
     * connection and handling and Redis.
     *
     * @param string $token Bot token given by BotFather.
     */
    public function __construct(string $token)
    {
        parent::__construct($token);

        $this->_message_commands = [];
        $this->_callback_commands = [];

        $this->keyboard = new Button($this);
        $this->status = new BotState($this);
        $this->local = new Localization($this);
        $this->database = new Database($this);
    }

    /** @} */
}
