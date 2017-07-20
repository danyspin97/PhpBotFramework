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
 * \addtogroup Commands
 * @{
 */

/** \class MessageRegexCommand
 */
class MessageRegexCommand extends BasicCommand
{
    /** @} */

    public static $type = 'message';

    public static $object_class = 'PhpBotFramework\Entities\Message';

    public static $priority = 1;

    private $regex_rule;

    public $args = null;

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
    public function __construct(string $regex_rule, callable $script)
    {
        $this->script = $script;
        $this->regex_rule = $regex_rule;
    }

    /**
     * @internal
     * \brief Process the message to check if it triggers a command of this type.
     * @param array $message Message to process.
     * @return bool True if the message triggered a command.
     */
    public function checkCommand(array $message) : bool
    {
        // If the message contains a bot command at the start
        $message_is_command = (isset($message['entities']) && $message['entities'][0]['type'] === 'bot_command') ? true : false;

        // Use preg_match to check if it is true
        if ($message_is_command
                && preg_match("/{$this->regex_rule}/",
                              substr($message['text'], $message['entities'][0]['offset'] + 1, $message['entities'][0]['length']),
                              $this->args)) {
            
            // first occurence is the matched expression, we want only arguments            
            array_shift($this->args);
            
            return true;
        }
        
        return false;
    }
    
}
