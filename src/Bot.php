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

use PhpBotFramework\Utilities\BotState;

use PhpBotFramework\Database\Database;

use PhpBotFramework\Database\Getter;
use PhpBotFramework\Database\LongPolling;

use PhpBotFramework\Localization\Localization;

/**
 * \class Bot Bot class that contains all modules.
 */
class Bot extends BasicBot
{
    use Getter,
        LongPolling;

    /**
     * \addtogroup Bot Bot
     * \brief Properties and methods to handle your Telegrams bot.
     * \details Here're listed all the properties and methods that offers facilities for bot's basic features.
     * @{
     */

    /** \brief Store the inline keyboard. */
    public $keyboard;

    /** \brief Database handler object. */
    public $database;

    /** \brief Redis connection. */
    public $redis;

    /** \brief Localization handler object. */
    public $local;

    /** \brief Status handler object. */
    public $status;

    /**
     * \brief Construct an empty bot.
     * \details Construct a complete Telegram bot which can use localization, database and more other.
     *
     * @param string $token Bot token, you can request one through **BotFather** on Telegram.
     */
    public function __construct(string $token)
    {
        parent::__construct($token);

        $this->keyboard = new Button($this);
        $this->status = new BotState($this);
        $this->local = new Localization($this);
        $this->database = new Database($this);
    }

    /** @} */
}
