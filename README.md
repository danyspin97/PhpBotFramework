# Hades
[![Version Badge](https://img.shields.io/badge/version-0.3-lightgrey.svg?style=flat)]()
[![License Badge](https://img.shields.io/badge/license-LGPL-blue.svg?style=flat)]()
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/37c6ba26de864c1e966aa4813b538e96)](https://www.codacy.com/app/danyspin97/HadesWrapper?utm_source=gitlab.com&amp;utm_medium=referral&amp;utm_content=WiseDragonStd/HadesWrapper/&amp;utm_campaign=Badge_Grade)

*Hades* is a lightweight and high-extensible wrapper for Telegram Bots' APIs.

## Getting Started

### Prerequisites
- Php 7.0 or greater
- Database (required for getUpdates)
- Redis (optional)
- cURL
- Php package for your database (php-mysql for MySql, php-pgsql for Postgresql and so on)
- php-redis package
- php-curl
- Composer (to install this package and the required ones)
- Web server ([nginx](http://nginx.org/) reccomended)
- SSL certificate (required for webhook) (follow [these steps](https://devcenter.heroku.com/articles/ssl-certificate-self) to make a self-signed certificate or use [StartSSL](https://www.startssl.com/))

### Installation
The first step in order to use Hades is get it:

```
git clone https://gitlab.com/WiseDragonStd/HadesWrapper
```

## Features and roadmap

- [x] Bot class
    - [x] connectToDatabase
    - [x] connectToRedis
    - [x] getChatId
    - [x] setChatId
    - [x] setLocalization
    - [x] Api methods
        - [x] apiRequest
        - [ ] setWebhook
        - [x] getUpdates
            - [x] getUpdatesRedis
            - [x] getUpdatesDatabase
        - [x] sendMessage
            - [x] sendMessage
            - [x] sendMessageKeyboard
            - [x] sendReplyMessageKeyboard
        - [x] forwardMessage
        - [x] sendPhoto
        - [ ] sendAudio
        - [ ] sendDocument
        - [x] sendSticker
        - [ ] sendVideo
        - [ ] sendVoice
        - [ ] sendLocation
        - [ ] sendVenue
        - [ ] sendContact
        - [x] sendChatAction
        - [ ] getUserProfilePhotos
        - [ ] getFile
        - [ ] kickChatMember
        - [ ] leaveChat
        - [ ] unbanChatMember
        - [x] getChat
        - [ ] getChatAdministrators
        - [ ] getChatMembersCount
        - [ ] getChatMember
        - [x] answerCallbackQuery
            - [x] answerEmptyCallbackQuery
        - [x] editMessageText
            - [x] editInlineMessageText
            - [x] editMessageTextKeyboard
            - [x] editInlineMessageTextKeyboard
        - [ ] editMessageCaption
        - [x] editMessageReplyMarkup
            - [x] editInlineMessageReplyMarkup
        - [x] answerInlineQuery
            - [x] answerInlineQueryArticleSwitchPM
- [x] Inline_keyboard class
    - [x] addLevelButtons
    - [x] clearKeyboard
    - [x] getKeyboard
        - [x] getNoJSONKeyboard
    - [ ] getListKeyboard
    - [ ] getBackKeyboard
- [x] Inline_query_results
    - [x] newArticle
        - [x] newArticleKeyboard
    - [ ] newResult
    - [x] getResults
- [x] Database
    - [x] getPDO
    - [x] createTable
    - [x] insertTable
    - [x] deleteTables
- [ ] Templates
    - [ ] Webhook
    - [ ] getUpdateDatabase
    - [ ] getUpdateRedis
    - [ ] configuration
- [ ] Examples
    - [ ] Hellobot
- [x] Documentation
    - [x] Readme
    - [x] License
    - [x] Changelog
    - [ ] Function examples

## Author
This wrapper was developed by @WiseDragonStd.
- Bot, Inline_keyboard and Inline_query_results classes created by @danyspin97
- Database class created by @dom.theseeker

## [License](https://www.gnu.org/licenses/lgpl-3.0.en.html)
HadesWrapper is released under GNU Lesser General Public License.
You may copy, distribute and modify the software provided that modifications are described and licensed for free under LGPL-3. Derivatives works (including modifications) can only be redistributed under LGPL-3, but applications that use the wrapper don't have to be.
