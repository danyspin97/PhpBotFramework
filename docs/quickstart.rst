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

    // Create the bot object
    $bot = new PhpBotFramework\Bot("token");

---------------
Answer messages
---------------

.. code:: php

    $bot->answerUpdate["message"] = function ($bot, $message) {

        // Reply as an echo bot
        $bot->sendMessage($message->getText());

    };

--------------
Answer updates
--------------

As for messages, answering other updates requires the assignment of a
function to ``Bot::answerUpdate["entity"]``.

The function assigned must take 2 arguments:

``$bot``
  the bot object

``$entity``
  the entity attached to the update

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
    $bot->addCommand(new Commands\MessageCommand("start", $start_closure));

For a complete list of Commands, checkout the `Command List <commands.html>`__.

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

    $bot->processWebhookUpdate();

