<?php

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use PhpBotFramework\Entities\Message;

define('MESSAGES', 2);

class BotTest extends TestCase
{
    public function testCreateBot()
    {
        $MOCK_SERVER_PORT = getenv('MOCK_SERVER_PORT');

        if (!isset($MOCK_SERVER_PORT)) {
            echo "You need to define the port for the mock server to run.\n";
            exit(1);
        }

        $bot = new PhpBotFramework\Test\TestBot('FAKE_TOKEN');

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
        $bot->oldDispatch();

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

        $command = new PhpBotFramework\Commands\MessageCommand("start", function ($bot, Message $message) {
            $bot->chat_id = 'FAKE_CHAT_ID';
            $bot->sendMessage('This is a start message');
        });

        $bot->addCommand($command);

        $bot->initCommandsWrap();

        $bot->processFakeUpdate($message);

        $this->assertFileExists($filename);
    }

    /**
     * @depends testCreateBot
     */
    public function testAddCommands($bot)
    {
        $filename = 'tests/message_command.json';
        $message = json_decode(file_get_contents($filename), true);

        $start_command = new PhpBotFramework\Commands\MessageCommand("start",
          function ($bot, Message $message) {
            $bot->chat_id = 'FAKE_CHAT_ID';
            $bot->sendMessage('This is a start message');
          }
        );

        $about_command = new PhpBotFramework\Commands\MessageCommand('about',
          function ($bot, Message $message) {
            $bot->sendMessage('By PhpBotFramework');
          }
        );

        $bot->addCommands($start_command, $about_command);

        $bot->processFakeUpdate($message);

        $this->assertFileExists($filename);
    }
    /**
     * @depends testCreateBot
     */
    public function testAddMessageCommands($bot) {
        $filename = 'tests/message_command.json';
        $message = json_decode(file_get_contents($filename), true);

        $bot->addMessageCommand("start", function ($bot, Message $message) {
            $bot->chat_id = 'FAKE_CHAT_ID';
            $bot->sendMessage('This is a start message');
        });

        $bot->processFakeUpdate($message);

        $this->assertFileExists($filename);

    }

    /**
     * @depends testCreateBot
     */
    /*public function testDatabaseConnection($bot)
    {
        $bot->connect([
            'username' => 'postgres',
            'password' => '',
            'adapter' => 'pgsql',
            'dbname' => 'travis_ci_test'
        ]);

        $this->assertTrue(isset($bot->pdo));
    }

    /**
     * @depends testCreateBot
     */
    /*public function testAddingUserInsertAUserInDatabase($bot)
    {
        $chat_id = $bot->chat_id;

        // Add the user
        $bot->addUser($chat_id);

        $sth = $bot->pdo->prepare('SELECT COUNT(chat_id) FROM "User" WHERE chat_id = :chat_id');
        $sth->bindParam(':chat_id', $chat_id);

        try {
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $count = $sth->fetchColumn();

        // Assert that we inserted the user
        $this->assertEquals($count, 1);
    }

    /**
     * @depends testCreateBot
     * @depends testDatabaseConnection
     */
    /*public function testBroadcastMessageSendMessageToAllUser($bot)
    {
        $this->assertEquals($bot->broadcastMessage("This is a broadcasted message."), 1);
    }

    /**
     * @depends testCreateBot
     */
    /*public function TestConnectToRedis ($bot)
    {
        $redis = new Redis();

        $bot->redis = $redis;

        $this->assertTrue(isset($bot->redis));
    }*/

    /**
     * @depends testCreateBot
     * @depends testDatabaseConnection
     * @dataProvider providerLanguage
     */
    /*public function testSetAUserLanguageInsertLanguageInDatabase($language, $bot, $pdo)
    {
        $bot->setLanguageRedis($language);

        // Assert language given and language in redis are the same
        $this->assertEquals($language, $bot->getLanguageRedis());

        // Assert that bot current language and language in database are the same
        $this->assertEquals($bot->language, $bot->getLanguageDatabase());
    }

    public function providerLanguage()
    {
        return [
            ['it'],
            ['es'],
            ['ru'],
            ['en']
        ];
    }*/

    /**
     * @depends testCreateBot
     * @depends testDatabaseConnection
     */
    /*public function testLanguageIsLoadedInArray($bot, $pdo)
    {
        $bot->loadSingleLopcalization('en');

        // Localization has en file?
        $this->assertArrayHasKey('en', $bot->local);
    } */

    /**
     * @depends testCreateBot
     * @depends testDatabaseConnection
     * @dataProvider providerStringIndex
     */
    /*public function testLocalizatedStringIsTheSameInLocalizationFile($index, $language, $bot, $pdo)
    {
        // Set the language from redis
        $bot->setLanguageRedis($language);

        // Get all file strings
        $local = file_get_contents("tests/$index.json");

        // Assert that localizated string got from bot is the same as the  string in the file
        $this->assertEquals($bot->getStr($index), $local[$index]);
    }

    public function providerStringIndex()
    {
        return [
            ['HelloMsg', 'en'],
            ['StartMsg', 'it']
        ];
    }*/
}
