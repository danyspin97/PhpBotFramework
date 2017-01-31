<?php

namespace PhpBotFramework\Test;

use PhpBotFramework\Bot;

class TestBot extends Bot {

    use FakeUpdate;

    public function processMessage($message) {

        $this->setChatID(getenv("CHAT_ID"));

        $this->sendMessage("Message from <b>{$message['from']['first_name']}</b> saying: <i>{$message['text']}</i>");

    }

}
