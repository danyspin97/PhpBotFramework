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

/** \class MessageCommand
 */
class MessageCommand extends BasicCommand
{
    /** @} */

    public static $type = 'message';

    public static $object_class = 'PhpBotFramework\Entities\Message';

    public static $priority = 1;

    private $command;

    private $length;

    /**
     * \brief Add a function that will be executed everytime a message contain the selected command
     * \details Use this syntax:
     *
     *     addMessageCommand("start", function($bot, $message) {
     *         $bot->sendMessage("Hi"); });
     * @param string $command The command that will trigger this function (without slash). Eg: "start", "help", "about"
     * @param callable $script The function that will be triggered by a command.
     * Must take an object(the bot) and an array(the message received).
     */
    public function __construct(string $command, callable $script)
    {
        $this->command = "/$command";
        $this->script = $script;
        $this->length = strlen($command) + 1;
    }

    /**
     * @internal
     * \brief Process a message checking if it trigger any MessageCommand.
     * @param string $message Message to process.
     * @return bool True if the message trigger any command.
     */
    public function checkCommand(array $message) : bool
    {
        // If the message contains a bot command at the start
        $message_is_command = (isset($message['entities']) && $message['entities'][0]['type'] === 'bot_command') ? true : false;

        // If we found a valid command (check first lenght, then use strpos)
        if ($message_is_command && $this->length == $message['entities'][0]['length'] && mb_strpos($this->command, $message['text'], $message['entities'][0]['offset']) !== false) {
                    // Return
                    return true;
        }

        return false;
    }
}
