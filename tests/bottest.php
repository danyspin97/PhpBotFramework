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
    public function testDatabaseConnection($bot)
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
    public function testAddingUserInsertAUserInDatabase($bot)
    {
        $chat_id = $bot->getChatID();

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
     */
    public function testBroadcastMessageSendMessageToAllUser($bot)
    {
        $this->assertEquals($bot->broadcastMessage("This is a broadcasted message."), 1);
    }

    /**
     * @depends testCreateBot
     */
    public function TestConnectToRedis ($bot)
    {
        $redis = new Redis();

        $bot->redis = $redis;

        $this->assertTrue(isset($bot->redis));
    }

    /**
     * @depends testCreateBot
     * @dataProvider providerLanguage
     */
    public function testSetAUserLanguageInsertLanguageInDatabase($language, $bot)
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
    }

    /**
     * @depends testCreateBot
     */
    public function testLanguageIsLoadedInArray($bot)
    {
        $bot->loadSingleLocalization('en');

        // Localization has en file?
        $this->assertArrayHasKey('en', $bot->local);
    }

    /**
     * @depends testCreateBot
     * @dataProvider providerStringIndex
     */
    public function testLocalizatedStringIsTheSameInLocalizationFile($index, $language, $bot)
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
    }
}
