<?php

require './vendor/autoload.php';

class BotTest extends PHPUnit_Framework_TestCase
{
    public $bot;

    public function setUpBot() {
        $this->bot = new DanySpin97\PhpBotFramework\Bot("token");

    }
}
