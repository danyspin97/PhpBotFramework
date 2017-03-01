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

use PhpBotFramework\Entities\Message;

/**
 * \addtogroup Modules
 * @{
 */

/** \class MessageCommand
 */
trait MessageCommand
{
    /** @} */

    /** \brief Chat id of the current user/group/channel. */
    protected $_chat_id;

    /**
     * \addtogroup Commands
     * \brief What commands are
     * @{
     */

    /** \brief (<i>Internal</i>)Store the command triggered on message. */
    protected $_message_commands = [];

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
    public function addMessageCommand(string $command, callable $script)
    {
        $this->_message_commands[] = [
            'script' => $script,
            'command' => '/' . $command,
            'length' => strlen($command) + 1,
        ];
    }

    /**
     * \brief (<i>Internal</i>)Process a message checking if it trigger any MessageCommand.
     * @param string $message Message to process.
     * @return bool True if the message trigger any command.
     */
    protected function processMessageCommand(array $message) : bool
    {
        if (isset($message['entities']) && $message['entities'][0]['type'] === 'bot_command') {
            // For each command added by the user
            foreach ($this->_message_commands as $trigger) {
                // If we found a valid command (check first length, then use strpos)
                if ($trigger['length'] == $message['entities'][0]['length'] &&
                    mb_strpos($trigger['command'], $message['text'], $message['entities'][0]['offset']) !== false) {
                    // Execute the script.
                    $this->_chat_id = $message['chat']['id'];
                    $trigger['script']($this, new Message($message));

                    return true;
                }
            }
        }

        return false;
    }
}
