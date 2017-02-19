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

namespace PhpBotFramework\Database;

/**
 * \addtogroup Modules
 * @{
 */

/** \class User
 */
trait User
{
    /** @} */

    abstract function getChat($chat_id);

    abstract function setChatID($chat_id);

    /** PDO connection to the database. */
    public $pdo;

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /**
     * \addtogroup Users-handle Users handling
     * \brief Handle bot users on the database.
     * @{
     */

    /** \brief Table contaning bot users data in the SQL database. */
    public $user_table = '"User"';

    /** \brief Name of the column that represents the user id in the sql database */
    public $id_column = 'chat_id';

    /** \brief Add a user to the database.
     * \details Add a user to the database in Bot::$user_table table and Bot::$id_column column using Bot::$pdo connection.
     * @param string|int $chat_id chat ID of the user to add.
     * @return bool True on success.
     */
    public function addUser($chat_id) : bool
    {
        if (!isset($this->pdo)) {
            throw new BotException("Database connection not set");
        }

        // Create insertion query and initialize variable
        $query = "INSERT INTO $this->user_table ($this->id_column) VALUES (:chat_id)";

        $sth = $this->pdo->prepare($query);
        $sth->bindParam(':chat_id', $chat_id);

        try {
            $sth->execute();
            $success = true;
        } catch (PDOException $e) {
            echo $e->getMessage();

            $success = false;
        }

        $sth = null;
        return $success;
    }

    /**
     * \brief Send a message to every user available on the database.
     * \details Send a message to all subscribed users, change Bot::$user_table and Bot::$id_column to match your database structure.
     * This method requires Bot::$pdo connection set.
     * All parameters are the same as CoreBot::sendMessage.
     * Because a limitation of Telegram Bot API the bot will have a delay after 20 messages sent in different chats.
     * @see CoreBot::sendMessage
     */
    public function broadcastMessage(
        string $text,
        string $reply_markup = null,
        string $parse_mode = 'HTML',
        bool $disable_web_preview = true,
        bool $disable_notification = false
    ) {
    

        if (!isset($this->pdo)) {
            throw new BotException("Database connection not set");
        }

        $sth = $this->pdo->prepare("SELECT $this->id_column FROM $this->user_table");

        try {
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        // Iterate over all the row got
        while ($user = $sth->fetch()) {
            $user_data = $this->getChat($user[$this->id_column]);

            if ($user_data !== false) {
                // Change the chat_id for the next API method
                $this->setChatID($user[$this->id_column]);
                $this->sendMessage($text, $reply_markup, null, $parse_mode, $disable_web_preview, $disable_notification);
            }
        }

        $sth = null;
    }

    /** @} */

    /** @} */
}
