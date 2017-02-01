<?php

namespace PhpBotFramework\Core;

trait Chat {

    abstract protected function execRequest(string $url, string $method);

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
    public function getMe() {

        return $this->execRequest('getMe?');

    }

    /**
     * \brief Get info about a chat.
     * \details Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.). [Api reference](https://core.telegram.org/bots/api#getchat)
     * @param int|string $chat_id Unique identifier for the target chat or username of the target supergroup or channel (in the format <code>@channelusername</code>)
     * @return Array|false Information about the chat.
     */
    public function getChat($chat_id) {

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
    public function getChatAdministrators($_chat_id) {

        $parameters = [
            'chat_id' => $_chat_id,
        ];

        return $this->execRequest('getChatAdministrators?' . http_build_query($parameters));

    }

    /** @} */

    /** @} */

}
