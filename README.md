# Php Bot Framework
[![License Badge](https://img.shields.io/badge/license-LGPL--3.0+-blue.svg?style=flat)]()
[![Version_Badge](https://img.shields.io/badge/version-1.0.0-yellow.svg?style=flat)]()

*Php Bot Framework* is a lightweight Framework for Telegram Bots' APIs.

## Usage
```php
class HelloBot extends \WiseDragonStd\PhpBotFramework\Bot {
    public function processMessage() {
        if (isset($this->text)) {
            // Send back the text sent by the user
            $this->sendMessage($this->text);
            }
        }
    }
}
```

## Requisites
- Php 7.0 or greater
- cURL
- Composer (to install this package and the required ones)
- Web server (*required for webhook*) ([nginx](http://nginx.org/) reccomended)
- SSL certificate (*required for webhook*) (follow [these steps](https://devcenter.heroku.com/articles/ssl-certificate-self) to make a self-signed certificate or use [StartSSL](https://www.startssl.com/))

## Installation
The first step in order to use Hades is get it:

```shell
composer require wisedragonstd/hades-wrapper
composer install --no-dev
```

## Bot using this wrapper
- [@MyAddressBookBot](https://telegram.me/myaddressbookbot)
- [@Giveaways_Bot](https://telegram.me/giveaways_bot)

## Author
This wrapper was developed by @DanySpin97 and @domcorvasce.

## [License](https://www.gnu.org/licenses/lgpl-3.0.en.html)
HadesWrapper is released under GNU Lesser General Public License.
You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the wrapper don't have to be.
