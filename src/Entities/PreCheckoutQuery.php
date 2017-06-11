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

namespace PhpBotFramework\Entities;

/**
 * \addtogroup Entities Entities
 * \brief Telegram Entities.
 * @{
 */

/** \class PreCheckoutQuery
 * \brief This object represents an incoming PreCheckout query.
 */
class PreCheckoutQuery implements \ArrayAccess
{

    /** @} */

    use EntityAccess;

    /**
     * \brief Get payload for the current invoice's checkout.
     * @return string $payload The invoice's payload.
     */
    public function getPayload() : string
    {
        return $this->container['invoice_payload'];
    }

    /**
     * \brief Get chat ID of the chat where the message comes from.
     * @return $chat_id Chat ID.
     */
    public function getChatID()
    {
        return isset($this->container['from']) ? $this->container['from']['id'] : null;
    }


    /**
     * \brief (<i>Internal</i>) Get parameter to set to the bot.
     * \details Each time the bot receive a query,
     * the parameter _pre_checkout_query_id will be set to the ID of this callback.
     * @return array Array with the parameter name as "var" index, and the id in "id" index.
     */
    public function getBotParameter() : array
    {

        return ['var' => '_pre_checkout_query_id', 'id' => $this->container['id']];
    }

    /**
     * \brief Get ID of this callback query.
     * @return int $id ID of the callback.
     */
    public function getID() : int
    {

        return $this->container['id'];
    }
}
