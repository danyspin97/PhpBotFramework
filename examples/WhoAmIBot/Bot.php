<?php
/**
 * This is a simple Telegram bot which, given a message, returns all information
 * about the Telegram user who send it.
 *
 * Change the token to reflect your bot's one.
 */
require_once '../../vendor/autoload.php';

class WhoAmIBot extends PhpBotFramework\Bot {
    // Override 'processMessage' in order to intercept the message and
    // get information about its author (if forwarded, its original author).
    protected function processMessage($message) {
        // Check if the message was forward
        isset($message['forward_from']) ? $index = 'forward_from' : $index = 'from';

        $response = $this->prepareResponse($message[$index]);
        $this->sendMessage($response);
    }

    private function prepareResponse($user) {
        return '<strong>Message sent by </strong>' . $user['first_name'] . "\n" .
               '<strong>User ID: </strong>' . $user['id'] . "\n" .
               '<strong>Username: </strong>' . $user['username'] . "\n";
    }
}

$bot = new WhoAmIBot('BOT_TOKEN');

// Add a welcome message
$start_command = new PhpBotFramework\Commands\MessageCommand('start',
    function ($bot, $message) {
        $bot->sendMessage('<strong>Hey there!</strong> Send or forward me a text message :)');
    }
);

$bot->addCommand($start_command);
$bot->getUpdatesLocal();

