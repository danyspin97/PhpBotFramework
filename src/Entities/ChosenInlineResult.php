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

/** \class ChosenInlineResult
 * \brief Represents the result of an inline query that was chosen by the user.
 */
class ChosenInlineResult implements \ArrayAccess
{

    /** @} */

    use EntityAccess;

    /**
     * \brief Get result's query.
     * @return $query Null if it's empty.
     */
    public function getQuery() : string
    {
        return isset($this->container['query']) ? $this->container['query'] : null;
    }

    /**
     * \brief Get the chat ID where the result comes from.
     * @return $chat_id Chat ID.
     */
    public function getChatID()
    {
        return $this->container['from']['id'];
    }
}
