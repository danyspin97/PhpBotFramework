<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class KeyboardTest extends TestCase
{

    public $keyboard;

    public function setUp() {

        $this->keyboard = new PhpBotFramework\Entities\InlineKeyboard();

    }

    public function testInlineKeyboard() {

        $this->keyboard->addLevelButtons(['text' => 'First', 'callback_data' => '1'], ['text' => 'Second', 'callback_data' => '2']);

        $this->assertEquals(json_decode($this->keyboard->get(false), true), $this->keyboard->getArray());;

    }

}
