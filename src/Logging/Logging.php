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

namespace PhpBotFramework\Logging;

use PhpBotFramework\Exceptions\BotException;

/**
 * \class Logging
 * \brief Logging data and helper.
 */
trait Logging
{
    public $bot_name = 'phpbotframework_bot';

    private $chat_log = "";

    public $script_path;

    /**
     * \brief Set bot name for logging purporses.
     * @param string $name Bot name.
     */
    public function setBotName(string $name)
    {
        // TODO Check for trailing characters
        $this->bot_name = $name;
    }

    /**
     * \brief Get chat_id choosed for logging.
     * @return string Chat_id target.
     */
    public function getChatLog() : string
    {
        return $this->chat_log;
    }

    /**
     * \brief Set chat_id choosed for logging.
     * @param string $chat_id Chat_id choosed.
     * @param bool $skip_check Skip checking if the chat is valid (set true only if the bot is using webhook to get updates).
     */
    public function setChatLog(string $chat_id, bool $skip_check = false)
    {
        // Check that the bot can write in that chat
        if (!$skip_check && $this->getChat($chat_id) !== false) {
            $this->chat_log = $chat_id;
        }
    }

    /**
     * @internal
     * \brief Get the path of the script.
     * \description Get the path of the script when getUpdates is used so we can save the log in that folder.
     * @return string Path of the script.
     */
    public function getScriptPath() : string
    {
        $backtrace = debug_backtrace(
                defined("DEBUG_BACKTRACE_IGNORE_ARGS")
                ? DEBUG_BACKTRACE_IGNORE_ARGS
                : false
        );
        $top_frame = array_pop($backtrace);
        return dirname($top_frame['file']);
    }
}
