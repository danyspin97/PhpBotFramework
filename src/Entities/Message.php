<?php

namespace PhpBotFramework\Entities;

/**
 * \addtogroup Entities Entities
 * @{
 */

/** \class Message
 * \brief This object represents a message.
 */
class Message implements \ArrayAccess {

    /** @} */

    use EntityAccess;

    public function getText() : string {

        // Get text of the message if any
        return isset($this->container['text']) ? $this->container['text'] : null;

    }

    public function getChatID() {

        // Return the chat id
        return $this->container['chat']['id'];

    }

}