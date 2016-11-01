# Php Bot Framework
[![License Badge](https://img.shields.io/badge/license-LGPL--3.0+-blue.svg?style=flat)]()
[![Version_Badge](https://img.shields.io/badge/version-1.0.0-green.svg?style=flat)]()

*Php Bot Framework* is a framework for Telegram Bot API.
Designed to be fast and easy to use, it provides all the features a user need.
Take control of your bot using the command-handler system or the update type based function.

## Usage

```php
class HelloBot extends DanySpin97\PhpBotFramework\Bot {
    public function processMessage(&$message) {
        // Send back the text sent by the user
        $this->sendMessage($this->getText());
    }
}

$bot = new HelloBot("token");
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
composer require wisedragonstd/hades-wrapper
composer install --no-dev
```

## Bot using this wrapper
- [@MyAddressBookBot](https://telegram.me/myaddressbookbot)
- [@Giveaways_Bot](https://telegram.me/giveaways_bot)

## Author
This Framework is developed and manteined by @DanySpin97.

## [License](https://www.gnu.org/licenses/lgpl-3.0.en.html)
PhpBotFramework is released under GNU Lesser General Public License.
You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the wrapper don't have to be.
