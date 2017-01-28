<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class CoreBotTest extends TestCase {

    public $token = 'BOT_TOKEN';
    public $subject;

    public function __construct() {

        // Get token from env variable
        $this->token = getenv("BOT_TOKEN");

        if ($this->token == 'BOT_TOKEN') {
            echo "You need a valid bot token to run tests/corebottest.php.\n";
            exit(1);
        }

        $this->subject = new PhpBotFramework\Bot($this->token);

    }

    public function testSetChatIDAndGetChatIDReturnSameID() {

        $chat_id = getenv("CHAT_ID");

        // Set chat id
        $this->subject->setChatID($chat_id);

        // Assert that getChatID returns the same chat_id set with setChatID
        $this->assertEquals($chat_id, $this->subject->getChatID());

    }

    /**
     * @param string $text Text of the message to send
     * @param string $parse_mode Parse mode of the message to send
     * @dataProvider providerMessageTexts
     */
    public function testSendingMessageWillReturnTheSentMessage(string $text, string $parse_mode) {

        $this->subject->sendMessage($text, null, $parse_mode);

    }

    /**
     * provider for test
     */
    public function providerMessageTexts() {

        return [
            ["First message <i>with</i> *no* markdown", "null"],
            ["Second message with <i>html</i> _markdown_", "HTML"],
            ["Third message <b>with</b> *markdown*", "Markdown"]
        ];

    }

    /**
     * getWebhookInfo()
     * It can be used to get information about your bot's webhooks.
     * Returns an hash which contains all the data.
     */
    public function testGetWebhookInfo() {
        $response = $this->subject->getWebhookInfo();

        $this->assertEquals(is_array($response), true);
        $this->assertArrayHasKey('pending_update_count', $response);

        return;
    }

    /**
     * deleteWebhook()
     * Delete webhook if the user configured one.
     */
    public function testDeleteWebhook() {
        $response = $this->subject->deleteWebhook();

        $this->assertEquals($response, true);

        return;
    }

    /**
     * setWebhook($params)
     * Set bot's webhook.
     */
    public function testSetWebhook() {
        $response = $this->subject->setWebhook([
            'url' => 'https://example.com',
            'max_connections' => 5
        ]);

        $this->assertEquals($response, true);
        $this->subject->deleteWebhook();

        return;
    }

}
