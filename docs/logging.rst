=======
Logging
=======

PhpBotFramework implements logging features.

It automatically creates a logging in the bot folder if it is using ``getUpdates``, otherwise it uses the syslog.

-------
BotName
-------

To change the bot name used in the log:

.. code:: php

    $bot->setBotName("MyBot");

------------
Chat logging
------------

In addition to file log and syslog you can setup a chat where all error messages will be sent:

.. code:: php

    $bot->setChatLog("35818591");

