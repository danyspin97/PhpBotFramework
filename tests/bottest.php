<?php

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class BotTest extends TestCase {
    public $bot;

    public function testBot() {

        $this->bot = new DanySpin97\PhpBotFramework\Bot("token");

    }
}
