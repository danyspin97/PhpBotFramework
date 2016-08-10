<?php

require './vendor/autoload.php';

class BotTest extends PHPUnit_Framework_TestCase
{
    public $bot;

    public function setUpBot() {
        $this->bot = new \WiseDragonStd\HadesWrapper\Bot("token");
        $this->bot->connectToRedis();
        $this->bot->connectToDatabase("mysql", "hello_world_test", "root", "mysql");

    }
}
