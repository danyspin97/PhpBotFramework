When a command get triggered, the associated function get called.

Commands are checked in order of priority (based on the type of the
commands).

MessageCommands
---------------

MessageCommands get triggered when the message received contains a
``bot_command`` at the start.

.. code:: php

    $start_command = new PhpBotFramework\Commands\MessageCommands("start",
            function ($bot, $message) {

            }
