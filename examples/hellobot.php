<?php

use \WiseDragonStd\HadesWrapper;

class HelloBot extends Bot {
    public function processMessage() {
        if (isset($message['text'])) {
            // Send back the text sent by the user
            sendMessage($message['text']);
            }
        }
    }
}
