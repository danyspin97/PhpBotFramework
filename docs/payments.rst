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

------------------------------
Configure Payments credentials
------------------------------

The next step is let PhpBotFramework knows how handle the payments we receive by our users.

**PhpBotFramework 3** comes with a method named _setPayment_ to define the token and the currency
used by the bot:

.. code:: php

    $bot->addMessageCommand('donate', function ($bot, $message) {
      $bot->setPayment(getenv('PAYMENT_TOKEN'), getenv('PAYMENT_CURRENCY') || 'EUR');
    });

-----------------
Create an invoice
-----------------

This **invoice** will allow us to receive a fixed amount of money by the user and check
if (s)he payed or not.

.. code:: php

    $bot->sendInvoice('Donation', 'Basic Donation', 'basicDonation', ['Donation' => 1]);

You'll get something like that:

.. image:: https://i.imgur.com/RqRq02I.png

-------------------
Manage the checkout
-------------------

How do we know if the user filled the form and it's ready to pay us?

Well, PhpBotFramework integrates the **answerPreCheckoutQuery** methods which takes the incoming
**pre_checkout_query** (managed through `answerUpdate <https://phpbotframework.readthedocs.io/en/3.0-dev/quickstart.html#answer-messages>`__) and answer to it by returning additional delivery costs, errors or any kind of response:

.. code:: php

    $bot->answerUpdate['pre_checkout_query'] = function ($bot, $pre_checkout_query) {
      // Telegram uses a custom way to define the amount of money handled.
      // For instance, 1 EUR is represented like 100.
      $money_received = $pre_checkout_query['total_amount'] / 100;

      // For logging purpose.
      echo "Received '$money_received EUR'";

      $bot->sendMessage('Thanks for your donation!');
      $bot->answerPreCheckoutQuery(true);
    };

We're done!
