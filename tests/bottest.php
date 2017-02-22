<?php

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use PhpBotFramework\Entities\Message;

define('MESSAGES', 2);

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

        $bot = new PhpBotFramework\Test\TestBot($token);

        $this->assertInstanceOf('PhpBotFramework\Test\TestBot', $bot);

        return $bot;
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
        $bot->initCommandsWrap();

        $bot->processFakeUpdate($message);

        // Assert that the id of the message processed is equal to the id of the message to process
        $this->assertEquals($message['message']['message_id'], $bot->message_id);
    }

    public function providerFakeMessages()
    {
        $messages = [];

        for ($i = 1; $i < MESSAGES + 1; $i++) {
            $json_data = file_get_contents('tests/message_' . $i . '.json');

            $array = json_decode($json_data, true);

            $messages[
            ] = [$array['message']['from']['first_name'] => $array];
        }

        return $messages;
    }

    /**
     * @depends testCreateBot
     */
    public function testMessageCommands($bot)
    {
        $filename = 'tests/message_command.json';
        $message = json_decode(file_get_contents($filename), true);

        $closure = function ($bot, Message $message) {
            $bot->setChatID(getenv("CHAT_ID"));
            $bot->sendMessage("This is a start message");
        };

        $bot->addMessageCommand('start', $closure);

        $bot->initCommandsWrap();

        $bot->processFakeUpdate($message);

        $this->assertFileExists($filename);
    }

    /**
     * @depends testCreateBot
     */
    public function testDatabaseConnection($bot) {
        $bot->connect([
            'username' => 'postgres',
            'password' => '',
            'adapter' => 'pgsql',
        ]);

        $this->assertTrue(isset($bot->pdo));
    }

    /**
     * @depends testCreateBot
     */
    public function testAddingUser($bot) {
        $chat_id = $bot->getChatID();

        // Add the user
        $bot->addUser($chat_id);

        $bot->pdo->prepare('SELECT COUNT(chat_id) FROM "User" WHERE chat_id = :chat_id');
        $bot->pdo->bindParam(':chat_id', $chat_id);

        try {
            $bot->pdo->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $count = $bot->pdo->fetchColumn();

        // Assert that we inserted the user
        $this->assertEquals($count, 1);
    }

    /**
     * @depends testCreateBot
     */
    public function testBroadcastMessage($bot) {
        $this->assertEquals($bot->broadcastMessage("Hello"), 1);
    }
}
