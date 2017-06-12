<?php
require './vendor/autoload.php';

class DonateBot extends PhpBotFramework\Bot {
    // Receive pre-checkout query.
    protected function processPreCheckoutQuery($pre_checkout_query) {
       $money_donated = $pre_checkout_query['total_amount'] / 100;
       $currency = $pre_checkout_query['currency'];

       echo "** Checkout for $currency $money_donated\n";
       $this->answerPreCheckoutQuery(true);

       // If a user donated more than 2 euros or dollars or whatever currency.
       if ($money_donated > 2) {
           $this->sendMessage('Thanks for your donation');
       }
    }
}


// Add commands
$bot = new DonateBot('BOT_TOKEN');
$bot->setPayment('PROVIDER_TOKEN', 'EUR');

$bot->addMessageCommand('start', function ($bot, $message) {
    $bot->sendInvoice('Donation', 'Small donation', 'don1', array('Donation' => 2));
    $bot->sendInvoice('Donation', 'Medium donation', 'don2', array('Donation' => 5));
    $bot->sendInvoice('Donation', 'Large donation', 'don3', array('Donation' => 10));
});

$bot->getUpdatesLocal();
