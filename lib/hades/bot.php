<?php
class Bot
{
	public $API_URL;

	/**
	 * Initialize a new bot requiring the bot's token as main argument
	 * If the token isn't specified raise an error
	 */

    function __construct($token=null)
    {
        if($token == null)
    	  	throw new Exception("Expected bot's token");
        $this->API_URL = 'https://api.telegram.org/bot' . $token . '/';
    }
}
?>