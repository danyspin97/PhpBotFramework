<?php
require_once './vendor/autoload.php';

$bot = new PhpBotFramework\Bot(getenv('BOT_TOKEN'));

$bot->answerUpdate['pre_checkout_query'] = function ($bot, $pre_checkout_query) {
  // Telegram uses a custom way to define the amount of money handled.
  // For instance, 1 EUR is represented like 100.
  $money_received = $pre_checkout_query['total_amount'] / 100;

  // For logging purpose.
  echo "Received '$money_received EUR'";

  $bot->sendMessage('Thanks for your donation!');
  $bot->answerPreCheckoutQuery(true);
};

$bot->addMessageCommand('start', function ($bot, $message) {
  $bot->sendMessage('Hey there! Nice to see you. Type `/donate` to donate.');
});

$bot->addMessageCommand('donate', function ($bot, $message) {
  $bot->setPayment(getenv('PAYMENT_TOKEN'), 'EUR');
  $bot->sendInvoice('Donation', 'Basic Donation', 'basicDonation', ['Donation' => 1]);
});

$bot->getUpdatesLocal();
