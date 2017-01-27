<?php

namespace PhpBotFramework\Core;

trait Chat {

    abstract protected function exec_curl_request($url, $method);

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
     */
    public function getMe() {

        return $this->exec_curl_request('getMe?');

    }

    /**
     * \brief Get info about a chat.
     * \details Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.). [Api reference](https://core.telegram.org/bots/api#getchat)
     * @param Unique identifier for the target chat or username of the target supergroup or channel (in the format <code>@channelusername</code>)
     */
    public function getChat($_chat_id) {

        $parameters = [
            'chat_id' => $_chat_id,
        ];

        return $this->exec_curl_request('getChat?' . http_build_query($parameters));

    }

    /**
     * \brief Use this method to get a list of administrators in a chat.
     * @param Unique identifier for the target chat or username of the target supergroup or channel (in the format <code>@channelusername</code>)
     * @return On success, returns an Array of ChatMember objects that contains information about all chat administrators except other bots. If the chat is a group or a supergroup and no administrators were appointed, only the creator will be returned.
     */
    public function getChatAdministrators($_chat_id) {

        $parameters = [
            'chat_id' => $_chat_id,
        ];

        return $this->exec_curl_request('getChatAdministrators?' . http_build_query($parameters));

    }

    /** @} */

    /** @} */

}
