<?php

namespace PhpBotFramework\Entities;

class InlineQuery implements \ArrayAccess {

    use EntityAccess;

    public function getQuery() : string {

        // Get text of the message if any
        return isset($this->container['query']) ? $this->container['query'] : null;

    }

    public function getChatID() {

        // Return the chat id
        return $this->container['from']['id'];

    }

    public function getBotParameter() : array {

        return ['var' => '_inline_query_id', 'id' => $this->container['id']];

    }

}
