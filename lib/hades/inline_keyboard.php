<?php

class Inline_keyboard {
    private $inline_keyboard;

    public function __contruct() {
        $inline_keyboard = [];
    }

    public function __construct($array) {
        $inline_keyboard = $array;
    }

    public function &getKeyboard() {
        $reply_markup = ['inline_keyboard' => $inline_keyboard];
        return $reply_markup;
    }

    public function &getNoJSONKeyboard() {
        $reply_markup = ['inline_keyboard' => $inline_keyboard];
        json_encode($reply_markup);
        return $reply_markup;
    }

    public function addNewLevelButtons(array ...$buttons): array {
        $new_level = [];
        foreach($buttons as $key => $data) {
            array_push($new_level, [
                'text' => $data['text'],
                'callback_data' => $data['callback_data']
            ]);
        }
        array_push($inline_keyboard, [$new_level]);
    }
}
