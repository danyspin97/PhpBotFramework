==========
Payments
==========

-----------------------
What are Payments API?
-----------------------

Recently, Telegram released the Payments API which allows bots to handle money.

The payments are completely secure because they relies on third-party certified services:

- `Stripe <https://stripe.com/>`__
- `Yandex.Money <https://money.yandex.ru/new>`__
- `Payme <https://payme.uz/>`__

This new API extends the possibilities of a Telegram bot allowing developers to create "Telegram e-commerce stores" or
whatever crazy idea they can come up with.

**In this tutorial** we're going to see how use Payments API through PhpBotFramework
to create a **DonateBot**: a sort of "Donate Button" for Telegram folks!

----------------------
Prepare the playground
----------------------

The first thing to do is enable the Payments API for our new bot and configure a payment method so we can access to the
donations received by our generous users.

Like always, Telegram explains in-depth how to do so;
we can't do nothing better but link to `Telegram documentation <https://core.telegram.org/bots/payments>`__.

-----------------------
Create the bot skeleton
-----------------------

Now we're ready to start developing our DonateBot!

Create a new directory wherever you want and install PhpBotFramework:

.. code:: bash

    mkdir donatebot
    composer require danyspin97/phpbotframework

Now create a new **Bot.php** where we're going to write bot's logic:

.. code:: php

    <?php
    require_once './vendor/autoload.php';

    $bot = new PhpBotFramework\Bot(getenv('BOT_TOKEN'));

    $bot->addMessageCommand('start', function sayHi($bot, $message) {
      $bot->sendMessage('Hey there! Nice to see you. Type `/donate` to donate.');
    });

    $bot->addMessageCommand('donate', function donate($bot, $message) {
      // payments logic goes here
      $bot->sendMessage('Coming soon');
    });

    $bot->getUpdatesLocal();


That is all we need to start a Telegram using PhpBotFramework.

Let us verify it works properly:

.. code:: bash

    export BOT_TOKEN=YOURTELEGRAMBOTTOKEN
    php Bot.php

Now go to Telegram and try it!

