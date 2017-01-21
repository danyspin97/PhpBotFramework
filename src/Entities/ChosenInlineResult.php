<?php

namespace PhpBotFramework\Entities;

class ChosenInlineResult implements \ArrayAccess {

    use EntityAccess;

    public function getQuery() : string {

        // Get text of the message if any
        return isset($this->container['query']) ? $this->container['query'] : null;

    }

    public function getChatID() {

        // Return the chat id
        return $this->container['from']['id'];

    }

}
