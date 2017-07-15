-------------------------
Create an Inline Keyboard
-------------------------

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

All button added will be on the same row.
Use:

.. code:: php

   $bot->keyboard->changeRow();

to switch to the next row.

You can also add more buttons on the same row using ``Keyboard::addLevelButtons``:

.. code:: php

   $bot->keyboard->addLevelButtons(
            [
               'text' => 'Button1',
               'callback_data' => 1
            ],
            [
               'text' => 'Button2',
               'callback_data' => 2
            ]
        );

This method will automatically change row after being called.
