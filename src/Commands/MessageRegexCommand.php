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

/** \class MessageRegexCommand
 */
trait MessageRegexCommand
{
    /** @} */

    /** \brief Chat ID of the current user/group/channel. */
protected $_chat_id;

    /**
     * \addtogroup Commands
     * \brief What commands are
     * @{
     */

    /** \brief (<i>Internal</i>)Store the command triggered on message. */
    protected $_message_regex_commands = [];

    /**
     * \brief Add a function that will be executed everytime a message contain a command
     * that match the regular expression.
     *
     * \details Use this syntax:
     *
     *     addMessageCommandRegex("number\d", function($bot, $message, $result) {
     *         $bot->sendMessage("You sent me a number"); });
     * @param string $regex_rule Regex rule that will called for evalueting the command received.
     * @param callable $script The function that will be triggered by a command.
     * Must take an object(the bot) and an array(the message received).
     */
    public function addMessageCommandRegex(string $regex_rule, callable $script)
    {
        $this->_message_commands[] = [
            'script' => $script,
            'regex_rule' => $regex_rule
        ];
    }

    /**
     * \brief (<i>Internal</i>) Process the message to check if it triggers a command of this type.
     * @param array $message Message to process.
     * @return bool True if the message triggered a command.
     */
    protected function processMessageRegexCommand(array $message) : bool
    {
        if (isset($message['entities']) && $message['entities'][0]['type'] === 'bot_command') {
            // For each command added by the user
            foreach ($this->_message_commands as $trigger) {
                if (preg_match("/{$trigger['regex_rule']}/", substr($message['text'], $message['entities'][0]['offset'] + 1, $message['entities'][0]['length']))) {
                    $this->_chat_id = $message['chat']['id'];

                    $trigger['script']($this, new Message($message));
                    return true;
                }
            }
        }

        return false;
    }
}
