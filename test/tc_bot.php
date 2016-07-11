<?php
include "../autoload.php";
use PHPUnit\Framework\TestCase;

class BotTest extends TestCase
{
	/** 
	 * Try to create a new bot's instance without specifying a token
	 * @expectedException Exception
	 */

	public function testNoToken()
	{
	  	new Bot();
	}

	/**
	 * Try to create a bot and then access to its API's URL
	 */

	public function testAPIURL()
	{
	  	$expected_value = 'https://api.telegram.org/botTHISISNTAVALIDAPIKEY/';
	  	$obj = new Bot("THISISNTAVALIDAPIKEY");

	    $this->assertEquals($expected_value, $obj->API_URL);
	}
}
?>