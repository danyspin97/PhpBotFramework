==========
Payments
==========

-----------------------
What are Payments API?
-----------------------

Recently, Telegram released the Payments API which allows bots (and developers) to receive money
from users without need to leave the chat.

The payments are managed by third-party services:

- `Stripe <https://stripe.com/>`__
- `Yandex.Money <https://money.yandex.ru/new>`__
- `Payme <https://payme.uz/>`__

While the API makes easy for a developer to know if (s)he was paid or not,
it's not the same for the users who doesn't have no warranty.

**PhpBotFramework 3.x** has introduced the support to the Payments API and the usage is really straightforward.

----------------------
Prepare the playground
----------------------

You can enable Payments API for your bot directly from **@BotFather**.

Like always, Telegram explains in-depth how to do so and we can't do nothing better
than link to `its documentation <https://core.telegram.org/bots/payments>`__.

------------------------------
Configure Payments credentials
------------------------------

PhpBotFramework comes with a method named **setPayment** which's used to set the necessary
data used by the bot to receive money (the provider token and the money currency).

.. code:: php

    $bot->setPayment(getenv('PAYMENT_TOKEN'), 'EUR');

The money currency should be represented following **ISO 4217 currency code**.

Learn more `here <https://core.telegram.org/bots/payments#supported-currencies>`__.

-----------------
Create an invoice
-----------------

A Telegram **invoice** is a special message which includes a form the user needs to fill
in order to send money to the bot.

PhpBotFramework provides the **sendInvoice** method; here's the basic usage:

.. code:: php

    $bot->sendInvoice('Donation', 'Basic Donation', 'basicDonation', ['Donation' => 1]);


Using the code above, you're going to get something like:

.. image:: https://i.imgur.com/RqRq02I.png

You can define various prices to pay:

.. code:: php

    $bot->sendInvoice('Donation', 'Basic Donation', 'basicDonation', ['Donation' => 1, 'Plus' => 1.5]);

And you can pass additional parameters to 'sendInvoice'. `Here <https://core.telegram.org/bots/api#sendinvoice>`__'s the complete list.

.. code:: php

    $bot->sendInvoice('Donation', 'Basic Donation', 'basicDonation', ['Donation' => 1], ['is_flexible' => true]);


-------------------
Shipping & Checkout
-------------------

When the user fills the form and the payment is ok, we need a way to tell the bot what to do next.

PhpBotFramework integrates the **answerPreCheckoutQuery** method which takes the incoming
**pre_checkout_query** (managed through `answerUpdate <https://phpbotframework.readthedocs.io/en/3.0-dev/quickstart.html#answer-messages>`__) and answer it by returning greetings, errors or any kind of response.

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

As we said, we can return an error if something goes wrong:

.. code:: php

     $bot->answerPreCheckoutQuery(false, 'I am too rich to allows other donations');

We can also return additional delivery costs if needed through **answerShipping**.

.. code:: php

     $bot->answerShipping(true, '', ['FedEx' => 3.99, 'USPS' => 4.20]);

