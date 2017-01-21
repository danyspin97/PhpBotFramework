<?php

namespace PhpBotFramework\Commands;

use PhpBotFramework\Entities\CallbackQuery;

trait CallbackCommand {

    /**
     * \addtogroup Commands
     * @{
     */

    /** \brief Store the command triggered on callback query. */
    protected $_callback_commands;

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

    /**
     * \brief (<i>Internal</i>) Process the callback query and check if it triggers a command of this type.
     * @param $callback_query Callback query to process.
     * @return True if the callback query triggered a command.
     */
    protected function processCallbackCommand(array $callback_query) : bool {

        // Check for callback commands
        if (isset($callback_query['data'])) {

            // Iterate over all commands
            foreach ($this->_callback_commands as $trigger) {

                // If command is found in callback data
                if (strpos($trigger['data'], $callback_query['data']) !== false) {

                    $this->_chat_id = $callback_query['message']['chat']['id'];

                    // Trigger the script
                    $trigger['script']($this, new CallbackQuery($callback_query));

                    // The callback triggered a command, return true
                    return true;
                }

            }

        }

        return false;

    }

    /** @} */

}
