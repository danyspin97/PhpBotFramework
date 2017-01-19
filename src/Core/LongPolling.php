<?php

namespace PhpBotFramework\Core;

trait LongPolling {

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

        // If offset is already set in redis
        if ($this->redis->exists($variable_name)) {

            // just set $offset as the same value
            $offset = $this->redis->get($variable_name);

        } else {
            // Else get the offset from the id from the first update received

            do {

                $update = $this->getUpdates(0, 1);

            } while (empty($update));

            $offset = $update[0]['update_id'];

            $this->redis->set($variable_name, $offset);

            $update = null;

        }

        $this->initBot();

        // Process all updates received
        while (true) {

            $updates = $this->getUpdates($offset, $limit, $timeout);

            // Parse all updates received
            foreach ($updates as $key => $update) {

                try {

                    $this->processUpdate($update);

                } catch (BotException $e) {

                    echo $e->getMessage();

                }

            }

            // Update the offset in redis
            $this->redis->set($variable_name, $offset + count($updates));
        }

    }

    /**
     * \brief Get updates received by the bot, and hold the offset in $offset.
     * \details Get the update_id of the first update to parse, set it in $offset and
     * then it start an infinite loop where it processes updates and keep $offset on the update_id of the last update received.
     * Each processUpdate() method call is surrounded by a try/catch.
     * @see getUpdates
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     */
    public function getUpdatesLocal(int $limit = 100, int $timeout = 60) {

        $update = [];

        // While there aren't updates to process
        do {

            // Get updates from telegram
            $update = $this->getUpdates(0, 1);

            // While in the array received there aren't updates
        } while (empty($update));

        // Set the offset to the first update recevied
        $offset = $update[0]['update_id'];

        $update = null;

        $this->initBot();

        // Process all updates
        while (true) {

            // Set parameter for the url call
            $parameters = [
                'offset' => $offset,
                'limit' => $limit,
                'timeout' => $timeout
            ];

            $updates = $this->exec_curl_request($this->_api_url . 'getUpdates?' . http_build_query($parameters));

            // Parse all update to receive
            foreach ($updates as $key => $update) {

                try {

                    // Process one at a time
                    $this->processUpdate($update);

                } catch (BotException $e) {

                    echo $e->getMessage();

                }

            }

            // Update the offset
            $offset += sizeof($updates);

        }

    }

    /**
     * \brief Get updates received by the bot, using the sql database to store and get the last offset.
     * \details It check if an offset exists on redis, then get it, or call getUpdates to set it.
     * Then it start an infinite loop where it process updates and update the offset on redis.
     * Each update is surrounded by a try/catch.
     * @see getUpdates
     * @param $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param $timeout <i>Optional</i>. Timeout in seconds for long polling.
     * @param $table_name <i>Optional</i>. Name of the table where offset is saved in the database
     * @param $column_name <i>Optional</i>. Name of the column where the offset is saved in the database
     */
    public function getUpdatesDatabase(int $limit = 100, int $timeout = 0, string $table_name = 'telegram', string $column_name = 'bot_offset') {

        if (!isset($this->_database)) {

            throw new BotException("Database connection is not set");

        }

        // Get the offset from the database
        $sth = $this->pdo->prepare('SELECT ' . $column_name . ' FROM ' . $table_name);

        try {

            $sth->execute();

        } catch (PDOException $e) {

            echo $e->getMessage();

        }

        $offset = $sth->fetchColumn();
        $sth = null;

        // Get the offset from the first update to update
        if ($offset === false) {

            do {

                $update = $this->getUpdates(0, 1);

            } while (empty($update));

            $offset = $update[0]['update_id'];

            $update = null;

        }

        // Prepare the query for updating the offset in the database
        $sth = $this->pdo->prepare('UPDATE "' . $table_name . '" SET "' . $column_name . '" = :new_offset');

        $this->initBot();

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

}
