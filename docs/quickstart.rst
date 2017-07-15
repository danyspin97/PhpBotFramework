==========
Quickstart
==========

------------
Create a bot
------------

.. code:: php

    <?php
    // Require the Composer's autoloader
    require 'vendor/autoload.php';

    /* All code from now will be assumed to use the
     * framework namespace */
    namespace PhpBotFramework;

    // Create the bot object
    $bot = new Bot("token");

---------------
Answer messages
---------------

.. code:: php

    $bot->answerUpdate["message"] = function ($bot, $message) {

        // Reply as an echo bot
        $bot->sendMessage($message->getText());

    }

--------------
Answer updates
--------------

As for messages, answering other updates requires the assignment of a
function to ``Bot::answerUpdate["entity"]``.

The function assigned must take 2 arguments: - ``$bot``: the bot object
- ``$entity``: the entity attached to the update

Example: answer all CallbackQuery removing the loading circle.

.. code:: php

    $bot->answerUpdate["callback_query"] = function ($bot, $callback_query) {
        $bot->answerCallbackQuery();
    }

You can find all possible updates
`here <https://core.telegram.org/bots/api#update>`__.

-------------------------
Answer the /start command
-------------------------

.. code:: php

    // What the bot will answer?
    $start_closure = function ($bot, $message) {
        $bot->sendMessage("Hello stranger. This is my start message");
    };

    // Register the command
    $bot->addCommand(new Commands\MessageCommand("start", $start_closure);

For a complete list of Commands, checkout the `Command
List <03-commands.html>`__

---------------
Getting updates
---------------

There are two mutually exclusive ways of receiving updates for your bot:
- getUpdates - Webhooks

getUpdates
~~~~~~~~~~

At the end of your bot script add:

.. code:: php

    $bot->getUpdatesLocal();

Your bot will start asking updates to Telegram and will process them
using ``Bot::answerUpdate`` and Commands.

Webhooks
~~~~~~~~

**Warning**: *This method requires both a webserver and a SSL
certificate*.

Add this line at the end of your bot script:

.. code:: php

    $bot->processUpdateWebhook();

-----------------------
Connecting the database
-----------------------

PhpBotFramework uses a simple wrapper to handle the database.

Connection using the wrapper:

.. code:: php

    $bot->database->connect([
        'adapter' => 'pgsql',
        'username' => 'sysuser',
        'password' => 'mypassword',
        'dbname' => 'my_bot_db'
    ]);

Or if you connect using PDO, pass the PDO object to the framework to use
the facilities:

.. code:: php

    $bot->database->pdo = $yourPdoObject;

Then you can access your PDO object using:

.. code:: php

    $bot->getPdo();

--------------
InlineKeyboard
--------------

Inline keyboard are special objects that can be sent along with
messages.

``Bot::$keyboard`` is a wrapper for inline keyboard creation:

.. code:: php

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

For more information and guides about Inline Keyboard have a look
`here <04-entities.html#inlinekeyboard>`__
