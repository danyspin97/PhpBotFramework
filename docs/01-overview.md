---
currentMenu: overview
---

## Features
- Modular
- Flexible HTTP requests with [Guzzle](https://github.com/guzzle/guzzle)
- Fast and easy to use
- Implements `getUpdates` and `webhook`
- Command-handle system for messages and callback queries
- Easy **inline keyboard** creation
- InlineQuery cretion
- SQL Database facilities
- Localization
- Save status for interactive chats
- Upload local files

## Requirements

1. Php 7.0 or greater

2. Composer

### Webhook requirements

3. Webserver

4. SSL certificate

### Localization requirements

5. SQL Database

6. Redis database (Optional)

## Installation

In order to install PhpBotFramework you need to install [composer](https://getcomposer.com):

~~~
curl -sS https://getcomposer.org/installer | php
~~~

Then set the framework as a requirement for the bot and install the dependencies:

~~~
php composer.phar require danyspin97/php-bot-framework
php composer.phar install --no-dev
~~~

Alternatively, you can set the depency in the `composer.json` of your bot:

~~~
{
 {
   "require": {
      "danyspin97/php-bot-framework": "*"
   }
}
~~~

After installing it, you need to require the Composer's autoloader:

~~~
require 'vendor/autoload.php';
~~~

## Made with PhpBotFramework

- [MyAddressBookBot](https://github.com/DanySpin97/MyAddressBookBot): [Try it on Telegram](https://telegram.me/myaddressbookbot)
- [Giveaways_Bot](https://github.com/DanySpin97/GiveawaysBot): [Try it on Telegram](https://telegram.me/giveaways_bot)

## Authors

This framework is developed and mantained by [Danilo Spinella](https://github.com/DanySpin97) and [Dom Corvasce](https://github.com/domcorvasce).

## License

PhpBotFramework is released under the [GNU Lesser General Public License v3](https://www.gnu.org/licenses/gpl-3.0.en.html).

~~~
You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the framework don't have to be.
~~~
