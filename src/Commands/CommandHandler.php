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

/*
 * \class CommandHandler
 * \brief Handle all bot commands.
 * \details Add commands, check if commands trigger a user update.
 */
trait CommandHandler
{
    /**
     * \addtogroup Commands
     * \brief Bots command and usage.
     * @{
     */

    /** @internal
     * \brief Contains all command used by the bot. */
    private $_commands = [];

    /**
     * @internal
     * \brief Initialize commands to speed up processing.
     * \details Get all command that the bot handle, and put them in priority.
     */
    protected function initCommands()
    {
        $commands_temp = $this->_commands;
        $this->_commands = [];

        // Iterate over each
        foreach ($commands_temp as $command) {
            $this->_commands[$command::$type][] = $command;
        }

        foreach ($this->_commands as $index => $array) {
            // Sort them by priority
            uasort($this->_commands[$index], 'PhpBotFramework\Commands\CommandHandler::sortingPrior');
        }
    }

    /**
     * @internal
     * \brief Process updates prioritizing bot's commands over the general methods (e.g. BaseBot::processMessage())
     * @param array $update Update to process.
     * @return bool True if this update trigger any command.
     */
    protected function processCommands(array $update) : bool
    {
        // For each command active (checked by initCommands())
        foreach ($this->_commands as $entity => $commands) {
            foreach ($commands as $index => $command) {
                // If the update type is right and the update triggered a command
                if (isset($update[$entity]) && $command->checkCommand($update[$entity])) {
                    $entity = new $command::$object_class($update[$entity]);
                    $this->_chat_id = $entity->getChatID();
                    $command->getScript()($this, $entity);
                    // Return the id as we already processed this update
                    return true;
                }
            }
        }

        // Return -1 because this update didn't trigger any command.
        return false;
    }

    /**
     * \brief Add a command to the bot.
     * @param BasicCommand $command Command to add. Must be an object that inherits BasicCommand class.
     */
    public function addCommand(BasicCommand $command)
    {
        $this->_commands[] = $command;
    }

    /**
     * @internal
     * \brief Sort an array based on <code>prior</code> index value.
     * @param array $a First array.
     * @param array $b Second array.
     * @return int 1 If $a > $b, -1 if $a < $b, 0 otherwise.
     */
    public static function sortingPrior($a, $b)
    {
        $prior_a = $a::$priority;
        $prior_b = $b::$priority;
        if ($prior_a > $prior_b) {
            return 1;
        }

        if ($prior_a < $prior_b) {
            return -1;
        }

        if ($prior_a == $prior_b) {
            return 0;
        }
    }

    /** @} */
}
