<?php

require './vendor/autoload.php';
require 'examplebot.php';
require 'languages.php';
require 'data.php';

/*
 * Main script of the Bot using webhook
 * Each request sent ny a telegram client will be parsed here and
 * the respective function will be called
 */

use \WiseDragonStd\HadesWrapper;

// Set error reporting to skip PHP_NOTICE: http://php.net/manual/en/function.error-reporting.php
error_reporting(E_ALL & ~E_NOTICE);

$update = file_get_contents("php://input");
$update = json_decode($update, true);

if (!$update) {
  // receive wrong update, must not happen
  exit;
}

$bot = new ExampleBot($token);
$bot->setLocalization($localization);
$bot->setDatabase(new Database($driver, $dbname, $user, $password, $bot));
$bot->connectToRedis();
$bot->inline_keyboard = new InlineKeyboard($bot);
$bot->processUpdate($update);
$bot = null;
