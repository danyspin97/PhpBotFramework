<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class CoreBotTest extends TestCase {

    public function testCreateCoreBot() {

        // Get token from env variable
        $token = getenv("BOT_TOKEN");

        if (!isset($token)) {
            echo "You need a valid bot token to run tests/corebottest.php.\n";
            exit(1);
        }

        return new PhpBotFramework\Core\CoreBot($token);

    }

    /**
     * @depends testCreateCoreBot
     */
    public function testSetChatIDAndGetChatIDReturnSameID($bot) {

        $chat_id = getenv("CHAT_ID");

        // Set chat id
        $bot->setChatID($chat_id);

        // Assert that getChatID returns the same chat_id set with setChatID
        $this->assertEquals($chat_id, $bot->getChatID());

    }

    /**
     * provider for test
     */
    public function providerMessageText() {

        return [
            'no_markdown' => ["First message <i>with</i> *no* markdown", ""],
            'HTML' => ["Second message with <i>html</i> _markdown_", "HTML"],
            'Markdown' => ["Third message <b>with</b> *markdown*", "Markdown"]
        ];

    }

    /**
     * @param string $text Text of the message to send
     * @param string $parse_mode Parse mode of the message to send
     *
     * @depends testCreateCoreBot
     * @dataProvider providerMessageText
     */
    public function testSendingMessageWillReturnTheSentMessage($text, $parse_mode, $bot) {

        // Send a message
        $new_message = $bot->sendMessage($text, null, null, $parse_mode);

        // Is the response an array?
        $this->assertEquals(is_array($new_message), true);

        // Does the array have the text key?
        $this->assertArrayHasKey('text', $new_message);

    }

    /**
     * getWebhookInfo()
     * It can be used to get information about your bot's webhooks.
     * Returns an hash which contains all the data.
     *
     * @depends testCreateCoreBot
     */
    public function testGetWebhookInfo($bot) {
        $response = $bot->getWebhookInfo();

        $this->assertEquals(is_array($response), true);
        $this->assertArrayHasKey('pending_update_count', $response);

        return;
    }

    /**
     * deleteWebhook()
     * Delete webhook if the user configured one.
     *
     * @depends testCreateCoreBot
     */
    /*public function testDeleteWebhook() {
        $response = $this->subject->deleteWebhook();

        $this->assertEquals($response, true);

        return;
    }*/

    /**
     * setWebhook($params)
     * Set bot's webhook.
     *
     * @depends testCreateCoreBot
     */
    /*public function testSetWebhook() {
        $response = $this->subject->setWebhook([
            'url' => 'https://example.com',
            'max_connections' => 5
        ]);

        $this->assertEquals($response, true);
        $this->subject->deleteWebhook();

        return;
    }*/

}
