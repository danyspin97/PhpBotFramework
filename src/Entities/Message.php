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
 * @{
 */

/** \class Message
 * \brief This object represents a message.
 */
class Message implements \ArrayAccess
{
    /** @} */

    use EntityAccess;

    public function getText() : string
    {
        // Get text of the message if any
        return isset($this->container['text']) ? $this->container['text'] : null;
    }

    public function getChatID()
    {
        // Return the chat id
        return $this->container['chat']['id'];
    }

    public function getMessageId() : int
    {
        return $this->container['message_id'];
    }
}
