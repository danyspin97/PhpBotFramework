---
currentMenu: quickstart
---

## Create a bot

~~~
<?php
// Require the Composer's autoloader
require 'vendor/autoload.php';

/* All code from now will be assumed to use the
 * framework namespace */
namespace PhpBotFramework;

// Create the bot object
$bot = new Bot("token");
~~~

## Answer the /start command

~~~
// What the bot will answer?
$start_closure = function ($bot, $message) {
    $bot->sendMessage("Hello stranger. This is my start message");
};

// Register the command
$bot->addCommand(new Commands\MessageCommand("start", $start_closure);
~~~

## Connecting the database

PhpBotFramework uses a simple wrapper to handle the database.

Connection using the wrapper:

~~~
$bot->database->connect([
    'adapter' => 'pgsql',
    'username' => 'sysuser',
    'password' => 'mypassword',
    'dbname' => 'my_bot_db'
]);
~~~

Or if you connect using PDO, pass the PDO object to the framework to use the facilities:

~~~
$bot->database->pdo = $yourPdoObject;
~~~

Then you can access your PDO object using:

~~~
$bot->getPdo();
~~~

## InlineKeyboard

Inline keyboard are special objects that can be sent along with messages.

`Bot::$keyboard` is a wrapper for inline keyboard creation:

~~~
// Answer /about messages
$bot->addCommand(new Commands\MessageCommand("about", function($bot, $message)
        {
            // Create an inline keyboard button with a link to a site
            $bot->keyboard->addButton("Link", "url", "example.com");

            // then send it with a message
            $bot->sendMessage("Visit our website!", $bot->keyboard->get());
        }
    )
);
~~~
