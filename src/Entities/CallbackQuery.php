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
 * \details If the button that originated the query was attached to a message sent by the bot,
 * the <code>field</code> message will be present.
 * If the button was attached to a message sent via the bot (in inline mode),
 * the field inline_message_id will be present.
 *
 * Exactly one of the fields data or <code>game_short_name</code> will be present.
 */
class CallbackQuery implements \ArrayAccess
{

    /** @} */

    use EntityAccess;

    /**
     * \brief Get data parameter if it is set.
     * @return string $data if set or empty string otherwise.
     */
    public function getData() : string
    {
        return isset($this->container['data']) ? $this->container['data'] : null;
    }

    /**
     * \brief Get chat ID of the chat where the message comes from.
     * @return $chat_id Chat ID.
     */
    public function getChatID()
    {
        return isset($this->container['message']) ? $this->container['message']['chat']['id'] : null;
    }

    /**
     * \brief Get message attached to this callback.
     * @return Message $message Message object attached to this callback.
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
     * \details Each time the bot receive a callback query the parameter _callback_query_id
     * will be set to the ID of this callback.
     * @return array Array with the parameter name as "var" index, and the id in "id" index.
     */
    public function getBotParameter() : array
    {

        return ['var' => '_callback_query_id', 'id' => $this->container['id']];
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
