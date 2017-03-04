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
        $this->script = function ($bot, $message) {
          // Check that the user can execute the command
          if (in_array($message['from']['id'], $ids) {
            $script($bot, $message);
          }
        };
    }
}
