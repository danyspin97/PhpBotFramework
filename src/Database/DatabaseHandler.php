<?php

namespace PhpBotFramework\Database;

trait DatabaseHandler {

    /**
     * \addtogroup Users-handle Users handling
     * \brief Handle bot users on the database.
     * @{
     */

    /** \brief Table contaning bot users data in the sql database. */
    public $user_table = '"User"';

    /** \brief Name of the column that represents the user id in the sql database */
    public $id_column = 'chat_id';

    /** \brief Add a user to the database.
     * \details Add a user to the database in Bot::$user_table table and Bot::$id_column column using Bot::$pdo connection.
     * @param $chat_id chat_id of the user to add.
     * @return True on success.
     */
    public function addUser($chat_id) : bool {

        // Is there database connection?
        if (!isset($this->pdo)) {

            throw new BotException("Database connection not set");

        }

        // Create insertion query and initialize variable
        $query = "INSERT INTO $this->user_table ($this->id_column) VALUES (:chat_id)";

        // Prepare the query
        $sth = $this->pdo->prepare($query);

        // Add the chat_id to the query
        $sth->bindParam(':chat_id', $chat_id);

        try {

            $sth->execute();
            $success = true;

        } catch (PDOException $e) {

            echo $e->getMessage();

            $success = false;

        }

        // Close statement
        $sth = null;

        // Return result
        return $success;

    }

    /**
     * \brief Broadcast a message to all user registred on the database.
     * \details Send a message to all users subscribed, change Bot::$user_table and Bot::$id_column to match your database structure is.
     * This method requires Bot::$pdo connection set.
     * All parameters are the same as CoreBot::sendMessage.
     * Because a limitation of Telegram Bot API the bot will have a delay after 20 messages sent in different chats.
     * @see CoreBot::sendMessage
     */
    public function broadcastMessage($text, string $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = true, bool $disable_notification = false) {

        // Is there database connection?
        if (!isset($this->pdo)) {

            throw new BotException("Database connection not set");

        }

        // Prepare the query to get all chat_id from the database
        $sth = $this->pdo->prepare("SELECT $this->id_column FROM $this->user_table");

        try {

            $sth->execute();

        } catch (PDOException $e) {

            echo $e->getMessage();

        }

        // Iterate over all the row got
        while ($user = $sth->fetch()) {

            // Call getChat to know that this users haven't blocked the bot
            $user_data = $this->getChat($user[$this->id_column]);

            // Did they block it?
            if ($user_data !== false) {

                // Change the chat_id for the next API method
                $this->setChatID($user[$this->id_column]);

                // Send the message
                $this->sendMessage($text, $reply_markup, null, $parse_mode, $disable_web_preview, $disable_notification);

            }

        }

        // Close statement
        $sth = null;

    }

    /** @} */

}
