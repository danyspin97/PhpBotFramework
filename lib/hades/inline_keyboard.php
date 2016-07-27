<?php

class Inline_keyboard {
    protected $inline_keyboard;
    protected $bot;

    public function __construct($bot = null, $array = null) {
        $this->bot = $bot;
        $this->inline_keyboard = $array ?? array();
    }

    public function &getKeyboard() {
        $reply_markup = ['inline_keyboard' => $this->inline_keyboard];
        $reply_markup = json_encode($reply_markup);
        return $reply_markup;
    }

    public function &getNoJSONKeyboard() {
        $reply_markup = ['inline_keyboard' => $this->inline_keyboard];
        return $reply_markup;
    }

    public function addLevelButtons(...$buttons) {
        array_push($this->inline_keyboard, $buttons);
    }
}
