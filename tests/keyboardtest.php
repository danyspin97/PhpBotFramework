<?php

require './vendor/autoload.php';

class KeyboardTest extends PHPUnit_Framework_TestCase
{
    public function testInlineKeyboard() {
        $keyboard = new \WiseDragonStd\HadesWrapper\InlineKeyboard();
        $keyboard->addLevelButtons(['text' => 'First', 'callback_data' => '1'], ['text' => 'Second', 'callback_data' => '2']);
        $keyboard->getKeyboard();
    }
}
