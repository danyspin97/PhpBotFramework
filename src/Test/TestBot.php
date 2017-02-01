<?php

namespace PhpBotFramework\Test;

use PhpBotFramework\Bot;

use PhpBotFramework\Entities\Message;

class TestBot extends Bot {

    use FakeUpdate;

    public function processMessage(Message $message) {

        $this->setChatID(getenv("CHAT_ID"));

        $this->sendMessage("Message from <b>{$message['from']['first_name']}</b> saying: <i>{$message['text']}</i>");

    }

}
