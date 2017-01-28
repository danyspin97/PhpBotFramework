<?php

namespace PhpBotFramework\Entities;

/**
 * \addtogroup Entities Entities
 * @{
 */

/** \class ChosenInlineResult
 * \brief Represents a result of an inline query that was chosen by the user and sent to their chat partner.
 */
class ChosenInlineResult implements \ArrayAccess {

    /** @} */

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
