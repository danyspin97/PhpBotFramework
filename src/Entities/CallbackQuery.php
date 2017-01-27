<?php

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
class CallbackQuery implements \ArrayAccess {

    /** @} */

    use EntityAccess;

    public function getData() : string {

        // Get text of the message if any
        return isset($this->container['data']) ? $this->container['data'] : null;

    }

    public function getChatID() {

        // Return the chat id
        return isset($this->container['message']) ? $this->container['message']['chat']['id'] : null;

    }

    public function getMessage() {

        if (!is_a($this->container, 'PhpBotFramework\Entities\Message')) {

            $this->container['message'] = new Message($this->container['message']);

        }

        return $this->container['message'];

    }

    public function getBotParameter() : array {

        return ['var' => '_callback_query_id', 'id' => $this->container['id']];

    }

    public function getID() : int {

        return $this->container['id'];

    }

}
