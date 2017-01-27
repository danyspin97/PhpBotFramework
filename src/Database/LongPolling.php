<?php

namespace PhpBotFramework\Database;

trait LongPolling {

    abstract public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 60);

    abstract protected function initCommands();

    /**
     * \addtogroup Bot
     * @{
     */

    /**
     * \addtogroup LongPollingDatabase Long polling With Database
     * \brief Use getUpdates saving and getting offset in redis/sql-database.
     * @{
     */

    /**
     * \brief (<i>Internal</i>)Get first update offset in redis.
     * \details Called by getUpdatesRedis to get the offset saved in redis or to get it from telegram and save it in redis.
     * @param $offset_key Name of the variable where the offset is saved on Redis
     * @return Id of the first update to process.
     */
    protected function getUpdateOffsetRedis(string $offset_key) : int {

        // If offset is already set in redis
        if ($this->redis->exists($offset_key)) {

            // return the value saved
            return $this->redis->get($offset_key);

            // Else get the offset from the id from the first update received
        } else {

            do {

                $update = $this->getUpdates(0, 1);

            } while (empty($update));

            $offset = $update[0]['update_id'];

            $this->redis->set($offset_key, $offset);

            return $offset_key;

        }

    }

    /**
     * \brief Get updates received by the bot, using redis to save and get the last offset.
     * \details It check if an offset exists on redis, then get it, or call getUpdates to set it.
     * Then it start an infinite loop where it process updates and update the offset on redis.
     * Each update is surrounded by a try/catch.
     * @see getUpdates
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     * @param $offset_key <i>Optional</i>. Name of the variable where the offset is saved on Redis
     */
    public function getUpdatesRedis(int $limit = 100, int $timeout = 60, string $offset_key = 'offset') {

        // Check redis connection
        if (!isset($this->redis)) {

            throw new BotException("Redis connection is not set");

        }

        $offset = $this->getUpdateOffsetRedis();

        $this->initCommands();

        // Prepare the query for updating the offset in the database
        $sth = $this->pdo->prepare('UPDATE "' . $table_name . '" SET "' . $column_name . '" = :new_offset');

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
