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

use PhpBotFramework\Exceptions\BotException;

/**
 * \addtogroup Commands
 * @{
 */

/** \class MultiCharacterCommand
 */
class MultiCharacterCommand extends BasicCommand
{
    /** @} */

    public static $type = 'message';

    public static $object_class = 'PhpBotFramework\Entities\Message';

    public static $priority = 1;

    private $command;

    private $length;

    /**
     * \brief Add a function that will be executed everytime a message contain the selected command
     * \details Use this syntax to create a command:
     *
     *     $help_command = new PhpBotFramework\Commands\MultiCharacterCommand("help",
     *         function ($bot, $message) {
     *             $bot->sendMessage("This is a help message.");
     *         }, ['!', '.', '/']
     *     );
     *
     * Then you can add it to the bot's commands using <code>addCommand</code> method:
     *
     *     $bot->addCommand($help_command);
     *
     * @param string $command The command that will trigger this function (e.g. start)
     * @param callable $script The function that will be triggered by a command.
     * Must take an object(the bot) and an array(the message received).
     */
    public function __construct(string $command, callable $script, array $characters)
    {
        $chars_count = count($characters);
        if ($chars_count === 0)
        {
            throw new BotException("No character given for matching the command");
        }

        if ($chars_count === 1)
        {
            // Build a regex using the only character
            $this->regex_rule = $characters[0] . $command;
        }
        // Build regex including all characters
        // Eg: (!|.)command
        else
        {
            $this->regex_rule = '(' . $characters[0];
            foreach(array_slice($characters, 1) as $char)
            {
                $this->regex_rule .= '|' . preg_quote($char);
            }
            $this->regex_rule .= ')' . $command;
        }

        print_r($this->regex_rule);
        $this->script = $script;
    }

    /**
     * @internal
     * \brief Process a message checking if it trigger any MessageCommand.
     * @param string $message Message to process.
     * @return bool True if the message trigger any command.
     */
    public function checkCommand(array $message) : bool
    {
        // Check the regex
        if (preg_match("/{$this->regex_rule}/", $message['text'])) {
                    // Return
                    return true;
        }

        return false;
    }
}
