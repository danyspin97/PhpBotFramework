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
            uasort($this->_commands[$index], function ($a, $b) {
                return $a::$priority <=> $b::$priority;    
            });
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
            foreach ($commands as $command) {
                // If the update type is right and the update triggered a command
                if (isset($update[$entity]) && $command->checkCommand($update[$entity])) {
                    $entity = new $command::$object_class($update[$entity]);
                    $this->chat_id = $entity->getChatID();
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
     * \brief Add various commands at once.
     * @param ...BasicCommand $commands The commands to add.
     */
    public function addCommands(BasicCommand ...$commands)
    {
      foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    /**
     * \brief Add a message command to the bot.
     * @param string $command The command that will trigger the function (e.g. start).
     * @param callable $script The function that will be triggered by a command.
     */
    public function addMessageCommand(string $command, callable $script)
    {
      $this->_commands[] = new MessageCommand($command, $script);
    }

    /**
     * \brief Add a callback command to the bot.
     * @param string $data The data that will trigger the function.
     * @param callable $script The function that will be triggered by the data.
     */
    public function addCallbackCommand(string $data, callable $script)
    {
      $this->_commands[] = new CallbackCommand($data, $script);
    }

    /** @} */
}
