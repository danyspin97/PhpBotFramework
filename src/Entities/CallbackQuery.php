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

/** \class CallbackQuery
 * \brief This object represents an incoming callback query from a callback button in an inline keyboard.
 * \details If the button that originated the query was attached to a message sent by the bot, the field message will be present. If the button was attached to a message sent via the bot (in inline mode), the field inline_message_id will be present. Exactly one of the fields data or game_short_name will be present.
 */
class CallbackQuery implements \ArrayAccess
{

    /** @} */

    use EntityAccess;

    /**
     * \brief Get data parameter if it is set.
     * @return $data if set or empty string otherwise.
     */
    public function getData() : string
    {

        // Get text of the message if any
        return isset($this->container['data']) ? $this->container['data'] : null;
    }

    /**
     * \brief Get chat id of the chat in which the message attached to this callback has been send.
     * @return $chat_id Chat id of the chat.
     */
    public function getChatID()
    {

        // Return the chat id
        return isset($this->container['message']) ? $this->container['message']['chat']['id'] : null;
    }

    /**
     * \brief Get message attached to this callback.
     * @return $message Message class object attached to this callback.
     */
    public function getMessage()
    {

        if (!is_a($this->container, 'PhpBotFramework\Entities\Message')) {
            $this->container['message'] = new Message($this->container['message']);
        }

        return $this->container['message'];
    }

    /**
     * \brief (<i>Internal</i>) Get parameter to set to the bot.
     * \details Each time the bot receive a callback query the parameter _callback_query_id will be set to the id of this callback.
     * @return Array with the parameter name as "var" index, and the id in "id" index.
     */
    public function getBotParameter() : array
    {

        return ['var' => '_callback_query_id', 'id' => $this->container['id']];
    }

    /**
     * \brief Get if of this callback query.
     * @return $id Id of the callback.
     */
    public function getID() : int
    {

        return $this->container['id'];
    }
}
