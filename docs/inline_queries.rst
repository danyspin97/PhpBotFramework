==============
Inline Queries
==============

Inline queries have been added in January 2016.

The user can call your bot by simply writing its username in the chat and a query.

After having enabled the inline mode enabled (by sending ``/setinline`` to @BotFather) the bot will start receiving ``inline_query`` updates.

Use the method ``answerInlineQuery`` to asnwer these updates. It requires an array of results to show to the user.

-------
Results
-------

Let's create the results:

.. :code:: php

    $bot->answerUpdate["inline_query"] = function ($bot, $message) {

        $bot->results->newArticle("Result1", "This is the first result.");

        $bot->results->newArticle("Result2", "This is the second result.");

    };

Important

Remember that the parameter ``$reply_markup`` must not be encoded in JSON.

If you cannot see the answer of the bots but you don't get any error message neither, check the ``$reply_markup`` parameter.

-------------------
Sending the results
-------------------

When we have added all the results, we are ready to send them:

.. :code:: php

    $bot->answerUpdate["inline_query"] = function ($bot, $message) {

        // Creation of the results
        ...

        $bot->answerInlineQuery($bot->results->get());

    };

-----
Types
-----

The type used in the example is ``article``, have a look here_ for all types.

.. here_: https://core.telegram.org/bots/api#inlinequeryresult

To create a result of a different type you can use ``addResult``:

.. :code:: php

    $bot->answerUpdate["inline_query"] = function ($bot, $message) {

        $bot->results->addResult([
                'type' => 'photo',
                'photo_url' => 'https://www.gstatic.com/webp/gallery/1.jpg',
                'thumb_url' => 'https://www.gstatic.com/webp/gallery/1.jpg'
             ]);

        $bot->answerInlineQuery($bot->results->get());

     };

To add multiple results simultaneously:

.. :code:: php

    $bot->answerUpdate["inline_query"] = function ($bot, $message) {

        $bot->results->addResults([
                [
                    'type' => 'photo',
                    'photo_url' => 'https://www.gstatic.com/webp/gallery/1.jpg',
                    'thumb_url' => 'https://www.gstatic.com/webp/gallery/1.jpg'
                ],
                [
                    'type' => 'photo',
                    'photo_url' => 'https://www.gstatic.com/webp/gallery/2.jpg',
                    'thumb_url' => 'https://www.gstatic.com/webp/gallery/2.jpg'
                ]
        ]);

        $bot->answerInlineQuery($bot->results->get());

     };

