<?php

include "../autoload.php";

class BotTest extends PHPUnit_Framework_TestCase
{
    public $bot;

    public function setUpBot() {
        $this->bot = new Bot("token");
    }

    /*
     * @depends setUpBot
     */
    public function testDatabase() {
        $this->bot->connectToDatabase("mysql", "hello_world_test", "root", "mysql");
    }

    /*
     * @depends setUpBot
     */
    public function testRedis() {
        $this->bot->connectToRedis();
    }
}
