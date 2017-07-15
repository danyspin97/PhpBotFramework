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


-----------------
Broadcast message
-----------------

If you want to update your users with the bot changelog, or telling them an important news you can use the ``Database::broadcastMessage`` which will do the job for you:

.. code:: php

    $bot->broadcastMessage("Checkout my new bot @DonateBot.")

 This method takes the same parameters as ``Bot::sendMessage``.

 For working the database must have a ``"User"`` table with a ``chat_id`` row.
