<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

use PhpBotFramework\Entities\Message;

define('MESSAGES', 1);

class BotTest extends TestCase
{

    public function testCreateBot()
    {

        // Get token from env variable
        $token = getenv("BOT_TOKEN");

        if (!isset($token)) {
            echo "You need a valid bot token to run tests/corebottest.php.\n";
            exit(1);
        }

        return new PhpBotFramework\Test\TestBot($token);
    }

    /**
     * @param Message $message Message to process
     * @param TestBot $bot Bot that will process the message
     *
     * @depends testCreateBot
     * @dataProvider providerFakeMessages
     */
    public function testProcessFakeMessage($message, $bot)
    {

        $bot->processFakeUpdate($message);
    }

    public function providerFakeMessages()
    {

        $messages = [];

        for ($i = 1; $i < MESSAGES + 1; $i++) {
            $json_data = file_get_contents('tests/message_' . $i . '.json');

            $array = json_decode($json_data, true);

            $messages[] = $array;
        }

        return [$messages];
    }
}
