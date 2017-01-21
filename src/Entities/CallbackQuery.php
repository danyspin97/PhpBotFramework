<?php

namespace PhpBotFramework\Entities;

class CallbackQuery implements \ArrayAccess {

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
