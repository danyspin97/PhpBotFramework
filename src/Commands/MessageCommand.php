<?php

namespace DanySpin97\PhpBotFramework;

trait MessageCommand {

    /**
     * \addtogroup Commands
     * \brief What commands are
     * @{
     */

    /** \brief Store the command triggered on message. */
    protected $_message_commands;

    /** \brief Does the bot has message commands? Set by initBot. */
    protected $_message_commands_set;

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
            'regex_active' => false
        ];

    }

    /**
     * \brief Add a function that will be executed everytime a message contain a command that match the regex
     * \details Use this syntax:
     *
     *     addMessageCommandRegex("number\d", function($bot, $message, $result) {
     *         $bot->sendMessage("You sent me a number"); });
     * @param $regex_rule Regex rule that will called for evalueting the command received.
     * @param $script The function that will be triggered by a command. Must take an object(the bot) and an array(the message received).
     */
    public function addMessageCommandRegex(string $regex_rule, callable $script) {

        $this->_message_commands[] = [
            'script' => $script,
            'regex_active' => true,
            'regex_rule' => $regex_rule
        ];

    }


}
