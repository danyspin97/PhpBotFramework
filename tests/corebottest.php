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
        $this->assertEquals($response['pending_update_count'], 0);

        return;
    }
}
