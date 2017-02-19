<?php
/**
 * This is a simple example bot which repeat everything you send to it.
 * It's powered by PhpBotFramework.
 *
 * Change token to reflect your bot's one.
 */

require_once '../vendor/autoload.php';

class EchoBot extends PhpBotFramework\Bot {
  // Override processMessage in order to send user the same message it give us.
  protected function processMessage($message) {
    $this->sendMessage($message['text']);
  }
}

$bot = new EchoBot('YOUR_BOT_TOKEN');
$bot->getUpdatesLocal();

