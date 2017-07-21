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

Remember that the parameter ``$reply_markup`` must not be encoded in JSON.
