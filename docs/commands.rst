========
Commands
========

When a command get triggered, the associated function get called.

Commands are checked in order of priority (based on the type of the
commands).

---------------
MessageCommands
---------------

MessageCommands get triggered when the message received contains a
``bot_command`` at the start.

.. code:: php

    $start_command = new PhpBotFramework\Commands\MessageCommands("start",
            function ($bot, $message) {
                $bot->sendMessage("You just hit /start");
            });


---------------
CallbackCommand
---------------

CallbackCommands get triggered when an inline button containing the corresponding data is hit by the user.

.. code:: php

    $help_callback = new PhpBotFramework\Commands\CallbackCommand("help",
            function ($bot, $message) {
                // Edit the message which contains the inline button
                $bot->editMessageText("This message now contains helpful information");
                // Don't forget to call Bot::answerCallbackQuery to remove the updating circle in the button
                $bot->answerCallbackQuery();
            });

---------------------
MultiCharacterCommand
---------------------

MultiCharacterCommand get triggered by messages what contains the selected keyword, prefixed by one of the wanted characters:
]
.. :code:: php

    $about_command = new PhpBotFramework\Commands\MessageCommands("about",
            function ($bot, $message) {
                $bot->sendMessage("This bot was made by BotFather.");
            },
            ['!', '.', '/']);


 Either the messages starting with ``/start``, ``.start`` and ``!start`` will trigger this command.
