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

trait Updates
{

    abstract protected function execRequest(string $url);

    /**
     * \addtogroup Api Api Methods
     * @{
     */

    /**
     * \brief Set bot's webhook.
     * \details Set a webhook for the current bot in order to receive incoming
     * updates via an outgoing webhook.
     * @param $params See [Telegram API](https://core.telegram.org/bots/api#setwebhook)
     * for more information about the available parameters.
     */
    public function setWebhook(array $params)
    {

        return $this->execRequest('setWebhook?' . http_build_query($params));
    }

    /**
     * \brief Get information about bot's webhook.
     * \details Returns an hash which contains information about bot's webhook.
     * @return Array|false Webhook info.
     */
    public function getWebhookInfo()
    {

        return $this->execRequest('getWebhookInfo');
    }

    /**
     * \brief Delete bot's webhook.
     * \details Delete bot's webhook if it exists.
     */
    public function deleteWebhook()
    {

        return $this->execRequest('deleteWebhook');
    }

    /**
     * \brief Request bot updates.
     * \details Request updates received by the bot using method getUpdates of Telegram API. [Api reference](https://core.telegram.org/bots/api#getupdates)
     * @param int $offset <i>Optional</i>. Identifier of the first update to be returned. Must be greater by one than the highest among the identifiers of previously received updates. By default, updates starting with the earliest unconfirmed update are returned. An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id. The negative offset can be specified to retrieve updates starting from -offset update from the end of the updates queue. All previous updates will forgotten.
     * @param int $limit <i>Optional</i>. Limits the number of updates to be retrieved. Values between 1—100 are accepted.
     * @param int $timeout <i>Optional</i>. Timeout in seconds for long polling.
     * @return Array|false Array of updates (can be empty).
     */
    public function getUpdates(int $offset = 0, int $limit = 100, int $timeout = 60)
    {

        $parameters = [
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout,
        ];

        return $this->execRequest('getUpdates?' . http_build_query($parameters));
    }

    /**
     * \brief Set updates received by the bot for getUpdates handling.
     * \details List the types of updates you want your bot to receive. For example, specify [“message”, “edited_channel_post”, “callback_query”] to only receive updates of these types. Specify an empty list to receive all updates regardless of type.
     * Set it one time and it won't change until next setUpdateReturned call.
     * @param Array $allowed_updates <i>Optional</i>. List of updates allowed.
     */
    public function setUpdateReturned(array $allowed_updates = [])
    {

        // Parameter for getUpdates
        $parameters = [
            'offset' => 0,
            'limit' => 1,
            'timeout' => 0,
        ];

        // Exec getUpdates
        $this->execRequest('getUpdates?' . http_build_query($parameters)
                                               . '&allowed_updates=' . json_encode($allowed_updates));
    }

    /** @} */
}
