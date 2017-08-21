<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class KeyboardTest extends TestCase
{

    public $keyboard;

    public function setUp()
    {
        $this->keyboard = new PhpBotFramework\Entities\InlineKeyboard();
    }

    public function testInlineKeyboard()
    {
        $this->keyboard->addListKeyboard(4, 10);

        $this->keyboard->addLevelButtons(['text' => 'First', 'callback_data' => '1'], ['text' => 'Second', 'callback_data' => '2']);

        $this->keyboard->addButton('Third', 'callback_data', '3');

        $this->keyboard->nextRow();

        $this->keyboard->addButton('Fourth', 'callback_data', '4');


        $this->assertEquals(json_decode($this->keyboard->get(false), true), $this->keyboard->getArray());
        ;
    }
}
