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

namespace PhpBotFramework\Test;

use PhpBotFramework\Bot;

use PhpBotFramework\Entities\Message;

class TestBot extends Bot
{
    use FakeUpdate;

    public $message_id = 0;

    /*
     * \brief Process a message sending it to the user specified internally.
     * @param Message $message The message to send to the user.
     */
    public function processMessage(Message $message)
    {
        $this->chat_id = getenv("CHAT_ID");
        $this->sendMessage("Message from <b>{$message['from']['first_name']}</b> saying: <i>{$message['text']}</i>");

        $this->message_id = $message['message_id'];
    }

    public function initCommandsWrap()
    {
        $this->initCommands();
    }
}
