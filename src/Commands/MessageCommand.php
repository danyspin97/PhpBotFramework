<?php

namespace PhpBotFramework\Commands;

use PhpBotFramework\Entities\Message;

trait MessageCommand {

    /**
     * \addtogroup Commands
     * \brief What commands are
     * @{
     */

    /** \brief (<i>Internal</i>)Store the command triggered on message. */
    protected $_message_commands;

    /**
     * \brief Add a function that will be executed everytime a message contain the selected command
     * \details Use this syntax:
     *
     *     addMessageCommand("start", function($bot, $message) {
     *         $bot->sendMessage("Hi"); });
     * @param $command The command that will trigger this function (without slash). Eg: "start", "help", "about"
     * @param $script The function that will be triggered by a command. Must take an object(the bot) and an array(the message received).
     */
    public function addMessageCommand(string $command, callable $script) {

        $this->_message_commands[] = [
            'script' => $script,
            'command' => '/' . $command,
            'length' => strlen($command) + 1,
        ];

    }

    /**
     * \brief (<i>Internal</i>)Process a message checking if it trigger any MessageCommand.
     * @param $message Message to process.
     * @return True if the message triggered any command.
     */
    protected function processMessageCommand(array $message) : bool {

        // If the message contains a bot command at the start
        if (isset($message['entities']) && $message['entities'][0]['type'] === 'bot_command') {

            // For each command added by the user
            foreach ($this->_message_commands as $trigger) {

                // If we found a valid command (check first lenght, then use strpos)
                if ($trigger['length'] == $message['entities'][0]['length'] && mb_strpos($trigger['command'], $message['text'], $message['entities'][0]['offset']) !== false) {

                    // Set chat_id
                    $this->_chat_id = $message['chat']['id'];

                    // Execute script,
                    $trigger['script']($this, new Message($message));

                    // Return
                    return true;
                }

            }

        }

        // No command were triggered, return false
        return false;

    }

}
