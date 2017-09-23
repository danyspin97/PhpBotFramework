# Php Bot Framework
[![Total Downloads](https://poser.pugx.org/danyspin97/php-bot-framework/downloads)](https://packagist.org/packages/danyspin97/php-bot-framework)
[![Latest Stable Version](https://poser.pugx.org/danyspin97/php-bot-framework/v/stable)](https://packagist.org/packages/danyspin97/php-bot-framework)
[![Build Status](https://travis-ci.org/DanySpin97/PhpBotFramework.svg?branch=master)](https://travis-ci.org/DanySpin97/PhpBotFramework)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/6254e3eccc93497997dae21e57a452ac)](https://www.codacy.com/app/danyspin97/PhpBotFramework?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=DanySpin97/PhpBotFramework&amp;utm_campaign=Badge_Grade)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DanySpin97/PhpBotFramework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/DanySpin97/PhpBotFramework/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/DanySpin97/PhpBotFramework/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/DanySpin97/PhpBotFramework/?branch=master)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![License](https://poser.pugx.org/danyspin97/php-bot-framework/license)](https://packagist.org/packages/danyspin97/php-bot-framework)

<p align="center">
  <img src="logo.png" title="PhpBotFramework-Logo" width="50%">
</p>


*PhpBotFramework* is a reliable and complete framework for [Telegram Bot API](https://core.telegram.org/bots/api)
with support to **Payments APIs**.

Designed to be fast and easy to use, it provides all the features a user need in order to start developing Telegram bots.

## Usage

```php
<?php

// Include the framework
require './vendor/autoload.php';

// Create a bot
$bot = new PhpBotFramework\Bot("token");

// Create a command that will be triggered everytime the user send `/start`
$start_command = new PhpBotFramework\Commands\MessageCommand("start",
    function($bot, $message) {
        // Reply with a message
        $bot->sendMessage("Hello, folks!");
    }
);

$bot->addCommand($start_command);

// A shortcut for message commands.

$bot->addMessageCommand('about', function($bot, $message) {
  $bot->sendMessage('Powered by PhpBotFramework');
});

// Receive updates from Telegram using getUpdates
$bot->run(GETUPDATES);
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
- Upload local files
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

## FAQ

- **Why we don't implement asynchronous requests?**

   We use [Guzzle](https://github.com/guzzle/guzzle) in order to fire HTTP requests to Telegram Bot API.

   Unfortunately it doesn't implement real asynchronous requests but **a sort of**.

   Take a look to [this issue](https://github.com/guzzle/guzzle/issues/1127) for more information.

- **Why there isn't a `chat_id` parameter to pass for API methods?**

  PhpBotFramework is "smart" enough to set it as the current user, group or channel ID.
  Most of the frameworks out there requires you to specify the chat ID for every method's call but PhpBotFramework does it for you calling most API methods on the current chat.

## Examples

You can find a list of examples bot right in `examples/` folder.

All examples listed here are fully functional: you only need a valid **Telegram bot token**.

## Made with PhpBotFramework
- [MyAddressBookBot](https://github.com/DanySpin97/MyAddressBookBot): [Try it on Telegram](https://telegram.me/myaddressbookbot)
- [Giveaways_Bot](https://github.com/DanySpin97/GiveawaysBot): [Try it on Telegram](https://telegram.me/giveaways_bot)

## Testing

PhpBotFramework comes with a test suite you can run using **PHPUnit**.

You need to set `MOCK_SERVER_PORT` environment variable which tells PhpBotFramework
on which port run the **mock server** that allows the tests to be executed.

```shell
export MOCK_SERVER_PORT=9696
```

Now you can run the run the mock server:
```shell
./start_mock_server.sh
```

And run the test suite:
```shell
php vendor/bin/phpunit
```

## Author
This framework is developed and mantained by [Danilo Spinella](https://github.com/DanySpin97) and [Dom Corvasce](https://github.com/domcorvasce).

## License
PhpBotFramework is released under [GNU Lesser General Public License v3](https://www.gnu.org/licenses/lgpl-3.0.en.html).

You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the framework don't have to be.
