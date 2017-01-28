# Php Bot Framework
[![Total Downloads](https://poser.pugx.org/danyspin97/php-bot-framework/downloads)](https://packagist.org/packages/danyspin97/php-bot-framework)<Paste>
[![Latest Stable Version](https://poser.pugx.org/danyspin97/php-bot-framework/v/stable)](https://packagist.org/packages/danyspin97/php-bot-framework)
[![Build Status](https://travis-ci.org/DanySpin97/PhpBotFramework.svg?branch=master)](https://travis-ci.org/DanySpin97/PhpBotFramework)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/6254e3eccc93497997dae21e57a452ac)](https://www.codacy.com/app/danyspin97/PhpBotFramework?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=DanySpin97/PhpBotFramework&amp;utm_campaign=Badge_Grade)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DanySpin97/PhpBotFramework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/DanySpin97/PhpBotFramework/?branch=master)
[![License](https://poser.pugx.org/danyspin97/php-bot-framework/license)](https://packagist.org/packages/danyspin97/php-bot-framework)


*PhpBotFramework* is a reliable and complete framework for [Telegram Bot API](https://core.telegram.org/bots/api).

Designed to be fast and easy to use, it provides all the features a user need in order to start developing Telegram bots.

## Usage

```php
<?php

// Include the framework
require './vendor/autoload.php';

// Create a bot
$bot = new PhpBotFramework\Bot("token");

// Add a command that will be triggered everytime the user send `/start`
$bot->addMessageCommand("start",
    function($bot, $message) {
        // Reply with a message
        $bot->sendMessage("Hello, folks!");
    }
);

// Receive updates from Telegram using getUpdates
$bot->getUpdatesLocal();
```

## Features
- Modular: take only what you need
- Flexible HTTP requests with [Guzzle](https://github.com/guzzle/guzzle)
- Designed to be fast and easy to use
- Support for local updates and webhooks
- Support for the most important API methods
- Command-handle system for messages and callback queries
- Update type based processing
- Easy **inline keyboard** creation
- Inline query results' handler
- Database support and facilities
- Redis support
- Support for multilanguage bots
- Support for bot states
- Highly-documented

## Requisites
- PHP >= 7.0
- php-mbstring
- Composer (to install this package and the required ones)
- Web server: *required for webhook* (we recommend [nginx](http://nginx.org/))
- SSL certificate: *required for webhook* (follow [these steps](https://devcenter.heroku.com/articles/ssl-certificate-self) to make a self-signed certificate or use [Let's Encrypt](https://letsencrypt.org/))

## Installation
You can install PhpBotFramework using **Composer**.

Go to your project's folder and type:

```shell
composer require danyspin97/php-bot-framework
composer install --no-dev
```

## Documentation
Check the [documentation](https://danyspin97.github.io/PhpBotFramework/) for learning more about PhpBotFramework.

## Made with PhpBotFramework
- [MyAddressBookBot](https://github.com/DanySpin97/MyAddressBookBot): [Try it on Telegram](https://telegram.me/myaddressbookbot)
- [Giveaways_Bot](https://github.com/DanySpin97/GiveawaysBot): [Try it on Telegram](https://telegram.me/giveaways_bot)

## Testing

PhpBotFramework comes with a test suite you can run using **PHPUnit**.

You need a valid bot token and chat ID in order to run tests:

```shell
export BOT_TOKEN=YOURBOTTOKEN
export CHAT_ID=YOURCHATID
```

After you've set the necessary, you can run the test suite typing:

```shell
phpunit
```

## Author
This framework is developed and mantained by [DanySpin97](https://github.com/DanySpin97).

## License
PhpBotFramework is released under [GNU Lesser General Public License v3](https://www.gnu.org/licenses/lgpl-3.0.en.html).

You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the framework don't have to be.
