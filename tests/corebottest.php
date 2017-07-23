<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

// Defines images url
define('TREE_IMAGE', 'http://www.planwallpaper.com/static/images/2022725-wallpaper_625864_Iz6NK8G.jpg');
define('EYES_IMAGE', 'http://www.planwallpaper.com/static/images/wallpapers-7020-7277-hd-wallpapers.jpg');
define('PANDA_IMAGE', 'http://www.planwallpaper.com/static/images/wallpaper-11628192.jpg');
define('LOGO_IMAGE', './logo.png');

// Define document path and urls
define('MESSAGE_JSON', './tests/message_1.json');
define('PHP_TEST', './tests/corebottest.php');
define('PDF_TEST', 'http://www.lmpt.univ-tours.fr/~volkov/C++.pdf');

class CoreBotTest extends TestCase
{
    public $chat_id;
    public function testCreateCoreBot()
    {
        $MOCK_SERVER_PORT = getenv('MOCK_SERVER_PORT');

        if (!isset($MOCK_SERVER_PORT)) {
            echo "You need to define the port for the mock server to run.\n";
            exit(1);
        }


        $bot = new PhpBotFramework\Core\CoreBot('FAKE_TOKEN');

        $bot->_http = new \GuzzleHttp\Client([
            'base_uri' => "http://localhost:$MOCK_SERVER_PORT",
            'connect_timeout' => 5,
            'verify' => false,
            'timeout' => 60,
            'http_errors' => false
        ]);


        $this->assertInstanceOf('PhpBotFramework\Core\CoreBot', $bot);
        return $bot;
    }

    /**
     * @depends testCreateCoreBot
     */
    public function testSetChatIDAndGetChatIDReturnSameID($bot)
    {
        $bot->setChatID('CUSTOM_CHAT_ID');
        $this->assertEquals('CUSTOM_CHAT_ID', $bot->chat_id);
    }

    /**
     * @param string $text Text of the message to send
     * @param string $parse_mode Parse mode of the message to send
     * @param CoreBot $bot Bot object that will send the photo
     *
     * @depends testCreateCoreBot
     * @dataProvider additionProvider
     */
    public function testSendingMessageWillReturnTheSentMessage($text, $parse_mode, $bot)
    {
        $new_message = $bot->sendMessage($text, null, null, $parse_mode);
        $this->assertInstanceOf('PhpBotFramework\Entities\Message', $new_message);
        $this->assertEquals(html_entity_decode($new_message['text']), $text);
    }

    /**
     * provider for test
     */
    public function additionProvider()
    {
        return [
            ['First message <i>with</i> *no* markdown', ''],
            ['Second message with <i>html</i> _markdown_', 'HTML'],
            ['Third message <b>with</b> *markdown*', 'Markdown']
        ];
    }

    /**
     * @param string $photo Photo to send
     * @param string $caption Caption to sen along with the photo
     * @param CoreBot $bot Bot object that will send the photo
     *
     * @depends testCreateCoreBot
     * @dataProvider providerPhoto
     */
    public function testSendPhoto($photo, $caption, $bot)
    {
        $new_photo = $bot->sendPhoto($photo, null, $caption);

        $this->assertArrayHasKey('photo', $new_photo);
        $this->assertArrayHasKey('caption', $new_photo);

        // Are the caption equals?
        $this->assertEquals($new_photo['caption'], $caption);
    }

    public function providerPhoto()
    {
        return [
            'tree' => [TREE_IMAGE, 'What a fantastic tree.'],
            'eyes' => [EYES_IMAGE, 'Blue is the new black.'],
            'panda' => [PANDA_IMAGE, 'Oohh, there is a panda!'],
            'logo' => [LOGO_IMAGE, 'Here it is the logo.']
        ];
    }

    /**
     * @depends testCreateCoreBot
     */
    public function testGetChatReturnTheSameID($bot)
    {
        $chat = $bot->getChat('CUSTOM_CHAT_ID');
        $this->assertEquals('CUSTOM_CHAT_ID', $chat['id']);
    }

    /**
     * @depends testCreateCoreBot
     * @dataProvider providerDocument
     */
    public function testSendDocument($document, $caption, $bot)
    {
        $new_document = $bot->sendDocument($document, $caption);

        $this->assertArrayHasKey('document', $new_document);

        $this->assertArrayHasKey('caption', $new_document);

        $bot->sendDocument($new_document['document']['file_id']);;
    }

    public function providerDocument()
    {
        return [
            'message_json' => [MESSAGE_JSON, 'This is a simple json file.'],
            'php_test' => [PHP_TEST, 'This is a php file.'],
            'pdf' => [PDF_TEST, 'This is a programming book.']
        ];
    }

    /**
     * @depends testCreateCoreBot
     */
    public function testEditingMessageChangeText($bot)
    {
        $new_message = $bot->sendMessage('This message will be edited.');
        $text = 'This message has been edited.';

        $edited_message = $bot->editMessageText($new_message['message_id'], $text);

        $this->assertInstanceOf('PhpBotFramework\Entities\Message', $edited_message);
        $this->assertNotEquals($edited_message['text'], $new_message['text']);
        $this->assertEquals($edited_message['text'], $text);
    }

    /**
     * getWebhookInfo()
     * It can be used to get information about your bot's webhooks.
     * Returns an hash which contains all the data.
     *
     * @depends testCreateCoreBot
     */
    public function testGetWebhookInfo($bot)
    {
        $response = $bot->getWebhookInfo();
        $this->assertArrayHasKey('pending_update_count', $response);
    }

    /**
     * @depends testCreateCoreBot
     */
    public function testGenerateLabeledPrices($bot) {
      $method = $this->invokeMethod($bot, 'generateLabeledPrices');

      $response = $method->invokeArgs($bot, [['Donation' => 1.45]]);
      $this->assertEquals('[{"label":"Donation","amount":145}]', $response);

      $response = $method->invokeArgs($bot, [['Donation' => 14.50, 'Taxes' => 0.59]]);
      $this->assertEquals('[{"label":"Donation","amount":1450},{"label":"Taxes","amount":59}]', $response);

      $response = $method->invokeArgs($bot, [['Donation' => 0.592]]);
      $this->assertEquals('[{"label":"Donation","amount":59}]', $response);

      $response = $method->invokeArgs($bot, [['Donation' => 0.50]]);
      $this->assertEquals('[{"label":"Donation","amount":50}]', $response);

      $this->expectException(Exception::class);
      $method->invokeArgs($bot, [['Donation' => -23]]);
    }

    /**
     * @depends testCreateCoreBot
     */
    public function testGenerateShippingOptions($bot) {
      $method = $this->invokeMethod($bot, 'generateShippingOptions');

      $response = $method->invokeArgs($bot, [['FedEx' => ['Dispatching' => 14.99]]]);
      $this->assertEquals('[{"id":"1","title":"FedEx","prices":[{"label":"Dispatching","amount":1499}]}]', $response);

      $response = $method->invokeArgs($bot, [['FedEx' => [], 'USPS'  => []]]);
      $this->assertEquals('[{"id":"1","title":"FedEx","prices":[]},{"id":"2","title":"USPS","prices":[]}]', $response);
    }

    private function invokeMethod(&$object, $methodName) {
      $reflection = new \ReflectionClass(get_class($object));
      $method = $reflection->getMethod($methodName);

      $method->setAccessible(true);
      return $method;
    }
}
