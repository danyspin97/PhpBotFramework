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

trait Chat
{
    abstract protected function execRequest(string $url);

    /**
     * \addtogroup Bot
     * @{
     */

    /**
     * \addtogroup Api Api Methods
     * @{
     */

    /**
     * \brief A simple method for testing your bot's auth token.
     * \details Requires no parameters. Returns basic information about the bot in form of a User object. [Api reference](https://core.telegram.org/bots/api#getme)
     * @return Array|false Bot info
     */
    public function getMe()
    {
        return $this->execRequest('getMe?');
    }

    /**
     * \brief Get info about a chat.
     * \details Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.). [Api reference](https://core.telegram.org/bots/api#getchat)
     * @param int|string $chat_id Unique identifier for the target chat or username of the target supergroup or channel (in the format <code>@channelusername</code>)
     * @return Array|false Information about the chat.
     */
    public function getChat($chat_id)
    {
        $parameters = [
            'chat_id' => $chat_id,
        ];

        return $this->execRequest('getChat?' . http_build_query($parameters));
    }

    /**
     * \brief Use this method to get a list of administrators in a chat.
     * @param string $chat_id Unique identifier for the target chat or username of the target supergroup or channel (in the format <code>@channelusername</code>)
     * @return Array|false On success, returns an Array of ChatMember objects that contains information about all chat administrators except other bots. If the chat is a group or a supergroup and no administrators were appointed, only the creator will be returned.
     */
    public function getChatAdministrators($chat_id)
    {
        $parameters = [
            'chat_id' => $chat_id,
        ];

        return $this->execRequest('getChatAdministrators?' . http_build_query($parameters));
    }

    /** @} */

    /** @} */
}
