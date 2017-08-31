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

/**
 * \class Inline
 * \brief All inline API Methods.
 */
trait Config
{
    public $bot_name = 'phpbotframework_bot';

    public $chat_log = 0;

    public $script_path;

    public function setBotName(string $name)
    {
        // TODO Check for trailing characters
        $this->bot_name = $name;
    }

    public function setChatLog(string $chat_id)
    {
        // TODO Check if chat is valid
        $this->chat_log = $chat_id;
    }

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
