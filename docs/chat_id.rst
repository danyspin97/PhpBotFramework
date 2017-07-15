The framework saves the ``chat_id`` of the current user (for private chats), group or channel based on where the update comes from.

All the Bot API methods which don't take ``$chat_id`` as a parameter will target the current chat.

To target another chat you can use:

- useChatId
  - withChatId

---------
withChatId
---------

This framework method will call the requested Bot API method using the choosed chat_id, without changing the current one.

.. code:: php

   $contact_command = new PhpBotFramework\Commands\MessageCommand("contact",
           function ($bot, $message) {
               // This message will be sent to whom pressed /contact
               $bot->sendMessage("My creator has been called");

               // This message will always be sent to @another_username
               $bot->withChatId("31285239382", "sendMessage", "Someone is calling for you");
           });

---------
useChatId
---------

This method will execute all the logic inside assuming the ``chat_id`` is the one choosed instead of the current.

.. code:: php

    $bot->useChatId("25929619",
            function() use ($bot) {
                $bot->sendMessage("This is a new message");
                $bot->sendPhoto("logo.png");
            });

All methods inside the anonymous function will target the choosed ``chat_id``.
After the method will be called, the current chat will be the same as before.
