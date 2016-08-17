<?php

use \WiseDragonStd\HadesWrapper;

class ExampleBot extends Bot {
    public function processMessage() {
        $message = &$this->$update['message'];
        if (isset($message['text'])) {
            // Text sent by the user
            $text = &$message['text'];
            if (strpos($text, '/start') === 0) {

            }
        }
    }

    public function processCallbackQuery() {
         $callback_query = &$this->update['callback_query'];
         $this->chat_id = &$callback_query['from']['id'];
         $message_id = $callback_query['message']['message_id'] ?? null;
         $inline_message_id = $callback_query['inline_message_id'] ?? null;
         $data = $callback_query['data'];
         if (isset($data) && isset($this->chat_id)) {
             switch($data) {

             }
         }
    }
}
