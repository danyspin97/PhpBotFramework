# Php Bot Framework
[![Total Downloads](https://poser.pugx.org/danyspin97/php-bot-framework/downloads)](https://packagist.org/packages/danyspin97/php-bot-framework)<Paste>
[![Latest Stable Version](https://poser.pugx.org/danyspin97/php-bot-framework/v/stable)](https://packagist.org/packages/danyspin97/php-bot-framework)
[![Build Status](https://travis-ci.org/DanySpin97/PhpBotFramework.svg?branch=master)](https://travis-ci.org/DanySpin97/PhpBotFramework)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/6254e3eccc93497997dae21e57a452ac)](https://www.codacy.com/app/danyspin97/PhpBotFramework?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=DanySpin97/PhpBotFramework&amp;utm_campaign=Badge_Grade)
[![License](https://poser.pugx.org/danyspin97/php-bot-framework/license)](https://packagist.org/packages/danyspin97/php-bot-framework)


*Php Bot Framework* is a framework for Telegram Bot API.
Designed to be fast and easy to use, it provides all the features a user need.
Take control of your bot using the command-handler system or the update type based function.

## Usage

```php
<?php

// Include the framework
require './vendor/autoload.php';

// Create a bot
$bot = new DanySpin97\PhpBotFramework\Bot("token");

// Add a command that will be triggered everytime the user click "/start"
$bot->addMessageCommand("start",
    function($bot, $message) {
        // Reply with "Hello"
        $bot->sendMessage("Hello");
    }
);

// Received updates from telegram using getUpdates
$bot->getUpdatesLocal();
```

## Features
- Designed to be the fast and easy to use
- Support for getUpdates and webhooks
- Support for the most important API methods
- Command-handle system
- Update type based processing
- Easy inline keyboard creation
- Inline query results handler
- Sql database support
- Redis support
- Support for multilanguage bot
- Support for bot state
- Highly documented

## Requisites
- Php 7.0 or greater
- php-mbstring
- Composer (to install this package and the required ones)
- Web server (*required for webhook*) ([nginx](http://nginx.org/) reccomended)
- SSL certificate (*required for webhook*) (follow [these steps](https://devcenter.heroku.com/articles/ssl-certificate-self) to make a self-signed certificate or use [Let's Encrypt](https://letsencrypt.org/))

## Installation
To use this framework go in your project folder and execute these commands:

```shell
composer require danyspin97/php-bot-framework
composer install --no-dev
```

## Documentation
Check the documentation [Here](https://danyspin97.github.io/PhpBotFramework/) for more.

## Bot using this wrapper
- [@MyAddressBookBot](https://telegram.me/myaddressbookbot) ([Source](https://github.com/DanySpin97/MyAddressBookBot))
- [@Giveaways_Bot](https://telegram.me/giveaways_bot)

## Author
This Framework is developed and mantained by @DanySpin97.

## [License](https://www.gnu.org/licenses/lgpl-3.0.en.html)
PhpBotFramework is released under GNU Lesser General Public License.
You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the framework don't have to be.
