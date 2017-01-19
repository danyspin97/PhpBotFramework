<?php

namespace PhpBotFramework\Utilities;

trait BotState {

    /**
     * \addtogroup State
     * \brief Create a state based bot using these methods.
     * \details Bot will answer in different way based on the state.
     * Here is an example where we use save user credential using bot states:
     *
     *     <?php
     *
     *     // Include the framework
     *     require './vendor/autoload.php';
     *
     *     // Define bot state
     *     define("SEND_USERNAME", 1);
     *     define("SEND_PASSWORD", 2);
     *
     *     // Create the class for the bot that will handle login
     *     class LoginBot extends DanySpin97\PhpBotFramework\Bot {
     *
     *         // Add the function for processing messages
     *         protected function processMessage($message) {
     *
     *             switch($this->getStatus()) {
     *
     *                 // If we are expecting a username from the user
     *                 case SEND_USERNAME:
     *
     *                     // Save the username
     *
     *                     // Say the user to insert the password
     *                     $this->sendMessage("Please, send your password.");
     *
     *                     // Update the bot state
     *                     $this->setStatus(SEND_PASSWORD);
     *
     *                     break;
     *
     *                 // Or if we are expecting a password from the user
     *                 case SEND_PASSWORD:
     *
     *                     // Save the password
     *
     *                     // Say the user he completed the process
     *                     $this->sendMessage("The registration is complete");
     *
     *                     break;
     *                 }
     *
     *         }
     *
     *     }
     *
     *     // Create the bot
     *     $bot = new LoginBot("token");
     *
     *     // Create redis object
     *     $bot->redis = new Redis();
     *
     *     // Connect to redis database
     *     $bot->redis->connect('127.0.0.1');
     *
     *     // Create the awnser to the <code>/start</code> command
     *     $start_closure = function($bot, $message) {
     *
     *         // saying the user to enter a username
     *         $bot->sendMessage("Please, send your username.");
     *
     *         // and update the status
     *         $bot->setStatus(SEND_USERNAME);
     *     };
     *
     *     // Add the answer
     *     $bot->addMessageCommand("start", $start_closure);
     *
     *     $bot->getUpdatesLocal();
     * @{
     */

    /** \brief Status of the bot to handle data inserting and menu-like bot. */
    public $status;

    /**
     * \brief Get current user status from redis and set it in status variable.
     * \details Throw exception if redis connection is missing.
     * @param $default_status <i>Optional</i>. The default status to return in case there is no status for the current user.
     * @return The status for the current user, $default_status if missing.
     */
    public function getStatus(int $default_status = -1) : int {

        if (!isset($this->redis)) {

            throw new BotException('Redis connection not set');

        }

        if ($this->redis->exists($this->_chat_id . ':status')) {

            $this->status = $this->redis->get($this->_chat_id . ':status');

            return $this->status;

        }

        $this->redis->set($this->_chat_id . ':status', $default_status);
        $this->status = $default_status;
        return $default_status;

    }

    /** \brief Set the status of the bot in both redis and $status.
     * \details Throw exception if redis connection is missing.
     * @param $status The new status of the bot.
     */
    public function setStatus(int $status) {

        $this->redis->set($this->_chat_id . ':status', $status);

        $this->status = $status;

    }

    /** @} */

}
