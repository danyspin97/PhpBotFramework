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

use PhpBotFramework\Exceptions\BotException;

/**
 * \addtogroup Modules
 * @{
 */

/** \class LongPollingDatabase
 */
trait LongPollingDatabase
{
    /** @} */

    abstract public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 60);
    abstract protected function initCommands();

    /**
     * \addtogroup Bot
     * @{
     */

    /**
     * \addtogroup LongPollingDatabase Long polling With Database
     * \brief Use getUpdates saving and getting offset in Redis and database.
     * @{
     */

    /**
     * \brief (<i>Internal</i>)Get first update offset in Redis.
     * \details Called by getUpdatesRedis in order to get the saved offset in Redis or retrieve it from Telegram and save it.
     * @param string $offset_key Name of the variable where the offset is saved on Redis.
     * @return int Id of the first update to process.
     */
    protected function getUpdateOffsetRedis(string $offset_key) : int
    {
        $redis = $this->getRedis();
        if ($redis->exists($offset_key)) {
            return $redis->get($offset_key);
        } else {
            // Get offset by first update.
            do {
                $update = $this->getUpdates(0, 1);
            } while (empty($update));

            $offset = $update[0]['update_id'];

            $redis->set($offset_key, $offset);
            return $offset;
        }
    }

    /**
     * \brief Get updates received by the bot, and use Redis to save and get the last offset.
     * \details It check if an offset exists on Redis: then get it or call getUpdates to set it.
     * Then it start a loop where it process updates and update the offset on Redis.
     * Each update is surrounded by a try/catch.
     * @see getUpdates
     * @param int $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param int $timeout <i>Optional</i>. Timeout (in seconds) for long polling.
     * @param string $offset_key <i>Optional</i>. Name of the variable where the offset is saved on Redis.
     */
    public function getUpdatesRedis(int $limit = 100, int $timeout = 60, string $offset_key = 'offset')
    {
        $redis = $this->getRedis();

        $offset = $this->getUpdateOffsetRedis($offset_key);
        $this->initCommands();

         // Process all updates received
        while (true) {
            $updates = $this->getUpdates($offset, $limit, $timeout);

            foreach ($updates as $key => $update) {
                try {
                    $this->processUpdate($update);
                } catch (BotException $e) {
                    echo $e->getMessage();
                }
            }

            // Update the offset in redis
            $this->redis->set($offset_key, $offset + count($updates));
        }
    }

    /**
     * \brief (<i>Internal</i>) Get first update offset in database.
     * \details Called by getUpdatesDatabase to get the offset saved in database.
     * If the offset is not saved: it retrieve the offset from Telegram and save it on the database.
     * @param string $table_name Name of the table where offset is saved in the database.
     * @param string $column_name Name of the column where the offset is saved in the database.
     * @return int Id of the first update to process.
     */
    protected function getUpdateOffsetDatabase(string $table_name, string $column_name) : int
    {
        $sth = $this->pdo->prepare('SELECT ' . $column_name . ' FROM ' . $table_name);

        try {
            $sth->execute();
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }

        $offset = $sth->fetchColumn();

        $sth = null;

        // Get the offset from the first update to update.
        if ($offset === false) {
            do {
                $update = $this->getUpdates(0, 1);
            } while (empty($update));

            $offset = $update[0]['update_id'];
        }

        return $offset;
    }

    /**
     * \brief Get updates received by the bot, using the SQL database to store and get the last offset.
     * \details It check if an offset exists on redis, then get it, or call getUpdates to set it.
     * Then it start a loop where it process updates and update the offset on Redis.
     * Each update is surrounded by a try/catch.
     * @see getUpdates
     * @param int $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param int $timeout <i>Optional</i>. Timeout (in seconds) for long polling.
     * @param string $table_name <i>Optional</i>. Name of the table where offset is saved in the database.
     * @param string $column_name <i>Optional</i>. Name of the column where the offset is saved in the database.
     */
    public function getUpdatesDatabase(int $limit = 100, int $timeout = 0, string $table_name = 'telegram', string $column_name = 'bot_offset')
    {

        if (!isset($this->_database)) {
            throw new BotException("Database connection is not set");
        }

        $offset = $this->getUpdateOffsetDatabase($table_name, $column_name);
        $this->initCommands();

        // Prepare the query for updating the offset in the database
        $sth = $this->pdo->prepare('UPDATE ' . $table_name . ' SET ' . $column_name . ' = :new_offset');

        while (true) {
            $updates = $this->getUpdates($offset, $limit, $timeout);

            foreach ($updates as $key => $update) {
                try {
                    $this->processUpdate($update);
                } catch (BotException $e) {
                    echo $e->getMessage();
                }
            }

            // Update the offset on the database
            $sth->bindParam(':new_offset', $offset + sizeof($updates));
            $sth->execute();
        }
    }

    /** @} */

    /** @} */
}
