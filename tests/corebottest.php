<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class CoreBotTest extends TestCase {
    public $token = '326052248:AAFIBIvv6qKC2j9vSbIVutIb7U1kcJMdkog';
    public $subject;

    public function __construct() {
        $this->subject = new DanySpin97\PhpBotFramework\Bot($this->token);
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
