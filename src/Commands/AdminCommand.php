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

/** \class AdminCommand
 */
class AdminCommand extends MessageCommand
{
    /** @} */

    public static $type = 'message';

    public static $object_class = 'PhpBotFramework\Entities\Message';

    public static $priority = 1;

    private $command;

    private $length;

    /**
     * \brief Registers commands that can be triggered only by administrators.
     * \details It works like <code>MessageCommand</code> but it requires a
     * third argument: the list of Telegram IDs which represents the users
     * that can execute the command.
     *
     *     $start_command = new PhpBotFramework\Commands\AdminCommand("getusers",
     *         function ($bot, $message) {
     *             $bot->sendMessage("Hello, folks!");
     *         },
     *     array(3299130043, -439991220, 12221004));
     *
     * @param string $command The command that will trigger this function (e.g. start)
     * @param callable $script The function that will be triggered by a command.
     * @param array $ids The users who can execute the command.
     * Must take an object(the bot) and an array(the message received).
     */
    public function __construct(string $command, callable $script, array $ids)
    {
        $this->command = "/$command";
        $this->length = strlen($command) + 1;
        $this->script = $script;

        $this->ids = $ids;
    }

    /**
     * @internal
     * \brief Process a message checking if it trigger any MessageCommand.
     * @param string $message Message to process.
     * @return bool True if the message trigger any command.
     */
    public function checkCommand(array $message) : bool
    {
        $message_is_command = (isset($message['entities']) && $message['entities'][0]['type'] === 'bot_command') ? true : false;

        // If we found a valid command (check length at first, then use strpos)
        if ($message_is_command && $this->length == $message['entities'][0]['length'] &&
            mb_strpos($this->command, $message['text'], $message['entities'][0]['offset']) !== false) {

            // Check if user has the right privileges to execute the command.
            return in_array($message['from']['id'], $this->ids) ? true : false;
        }

        return false;
    }

}
