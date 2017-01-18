<?php

namespace DanySpin97\PhpBotFramework;

trait CallbackCommand {

    /**
     * \addtogroup Commands
     * @{
     */

    /** \brief Store the command triggered on callback query. */
    protected $_callback_commands;

    /** \brief Does the bot has message commands? Set by initBot. */
    protected $_callback_commands_set;

    /**
     * \brief Add a function that will be executed everytime a callback query contains a string as data
     * \details Use this syntax:
     *
     *     addMessageCommand("menu", function($bot, $callback_query) {
     *         $bot->editMessageText($callback_query['message']['message_id'], "This is the menu"); });
     * @param $data The string that will trigger this function.
     * @param $script The function that will be triggered by the callback query if it contains the $data string. Must take an object(the bot) and an array(the callback query received).
     */
    public function addCallbackCommand(string $data, callable $script) {

        $this->_callback_commands[] = [
            'data' => $data,
            'script' => $script,
        ];

    }

    /** @} */

}
