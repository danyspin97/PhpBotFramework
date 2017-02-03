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
 * \addtogroup Modules
 * @{
 */

/** \class CallbackCommandHandler
 */
trait CommandHandler
{
    /** @} */

    /**
     * \addtogroup Commands
     * \brief What commands are
     * @{
     */

    /** \brief (<i>Internal</i>)contains all command that can trigger the bot.
     * \details E.g. Add each type of command processed by the bot into this array to avoid overhead. */
    protected $_command_types;

    /**
     * \brief (<i>Internal</i>) Init commands to speed up processing.
     * \details Get all command that the bot handle, and put them in priority.
     */
    protected function initCommands()
    {
        // All command types with respective update
        static $commands = ['MessageCommand' =>
            ['var' => '_message_commands', 'update' => 'message', 'prior' => '1'],
            'CallbackCommand' =>
            ['var' => '_callback_commands', 'update' => 'callback_query', 'prior' => '1'],
            'MessageRegexCommand' => ['var' => '_message_regex_commands', 'update' => 'message', 'prior' => '2']];

        // Sort them by priority
        uasort($commands, 'PhpBotFramework\Commands\CommandHandler::sortingPrior');

        // Iterate over each
        foreach ($commands as $index => $command) {
            // If there is at least a command of that type (by checking that the container exists and it is not empty)
            if (isset($this->{$command['var']}) && !empty($this->{$command['var']})) {
                // Add the type to the command container
                $this->_command_types[] = ['method' => "process$index", 'update' => $command['update']];
            }
        }
    }

    /**
     * \brief Process updates handling first commands, and then general methods (e.g. BaseBot::processMessage())
     * @param array $update Update to process.
     * @return int Id of the update processed.
     */
    protected function processUpdate(array $update) : int
    {
        // For each command active (checked by initCommands())
        foreach ($this->_command_types as $index => $command) {
            // If the update type is right and the update triggered a command
            if (isset($update[$command['update']]) && $this->{$command['method']}($update[$command['update']])) {
                // Return the id as we already processed this update
                return $update['update_id'];
            }
        }

        // Call the parent method because this update didn't trigger any command
        return parent::processUpdate($update);
    }

    /**
        * \brief (<i>Internal</i>) Sort an array based on <code>prior</code> index value.
     * @param array $a First array.
     * @param array $b Second array.
     * @return int 1 If $a > $b, -1 if $a < $b, 0 otherwise.
     */
    public static function sortingPrior($a, $b)
    {
        if ($a['prior'] > $b['prior']) {
            return 1;
        }

        if ($a['prior'] < $b['prior']) {
            return -1;
        }

        if ($a['prior'] == $b['prior']) {
            return 0;
        }
    }

    /** @} */
}
