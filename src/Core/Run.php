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

namespace PhpBotFramework\Core;

/**
 * \brief Contains helper functions for running the bot
 */
trait Run
{
    abstract public function init();

    /**
     * \addtogroup Bot
     * @{
     */

    public function run(string $type)
    {
        $this->init();

        if ($type == "Webhook") {
            $this->processWebhookUpdate();
            return;
        }

        $this->getUpdates();
    }

    /** @} */

    /**
     * @internal
     * \brief Get update and process it.
     * \details Call this method if user is using webhook.
     * It'll get bot's update from php::\input, check it and then process it using <b>processUpdate</b>.
     */
    public function processWebhookUpdate()
    {
        $this->_is_webhook = true;

        $this->init();
        $update = json_decode(file_get_contents('php://input'), true);
        if (!$update) {
            throw new BotException("Empty webhook update received");
        }
        $this->processUpdate($update);
    }


    /**
     * @internal
     * \brief Get updates received by the bot, and hold the offset in $offset.
     * \details Get the <code>update_id</code> of the first update to parse, set it in $offset and
     * then it start an infinite loop where it processes updates and keep $offset on the update_id of the last update received.
     * Each processUpdate() method call is surrounded by a try/catch.
     * @see getUpdates
     * @param int $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param int $timeout <i>Optional</i>. Timeout in seconds for long polling.
     */
    public function getUpdatesLocal(int $limit = 100, int $timeout = 60)
    {
        $update = [];

        // While there aren't updates to process
        while (empty($update = $this->getUpdates(0, 1)));

        $offset = $update[0]['update_id'];

        // Process all updates
        while (true) {
            $updates = $this->execRequest("getUpdates?offset=$offset&limit=$limit&timeout=$timeout");

            foreach ($updates as $key => $update) {
                try {
                    $this->processUpdate($update);
                } catch (BotException $e) {
                    echo $e->getMessage();
                }
            }

            $offset += sizeof($updates);
        }
    }
}
