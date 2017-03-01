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

namespace PhpBotFramework\Commands;

/**
 * \addtogroup Commands
 * @{
 */

/** \class CallbackCommand
 */
class CallbackCommand extends BasicCommand
{
    /** @} */

    public static $type = 'callback_query';

    public static $object_class = 'PhpBotFramework\Entities\CallbackQuery';

    public static $priority = 1;

    private $data;

    /**
     * \brief Add a function that will be executed everytime a callback query contains a string as data
     * \details Use this syntax:
     *
     *     addMessageCommand("menu", function($bot, $callback_query) {
     *         $bot->editMessageText($callback_query['message']['message_id'], "This is the menu"); });
     * @param string $data The string that will trigger this function.
     * @param callable $script The function that will be triggered by the callback query if it contains the $data string. Must take an object(the bot) and an array(the callback query received).
     */
    public function __construct(string $data, callable $script)
    {
        $this->data = $data;
        $this->script = $script;
    }

    /**
     * \brief (<i>Internal</i>) Process the callback query and check if it triggers a command of this type.
     * @param array $callback_query Callback query to process.
     * @return bool True if the callback query triggered a command.
     */
    public function checkCommand(array $callback_query) : bool
    {
        // Check for callback commands
        if (isset($callback_query['data'])) {
            // If command is found in callback data
            if (strpos($this->data, $callback_query['data']) !== false) {
                return true;
            }
        }

        return false;
    }

    /** @} */

    /** @} */
}
