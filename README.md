# Hades
![Version Badge](https://img.shields.io/badge/version-0.3-lightgrey.svg?style=flat) ![License Badge](https://img.shields.io/badge/license-AGPLv3-blue.svg?style=flat) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/37c6ba26de864c1e966aa4813b538e96)](https://www.codacy.com/app/danyspin97/HadesWrapper?utm_source=gitlab.com&amp;utm_medium=referral&amp;utm_content=WiseDragonStd/HadesWrapper/&amp;utm_campaign=Badge_Grade)

*Hades* is a lightweight and high-extensible wrapper for Telegram Bots' APIs.

## Getting Started

The first step in order to use Hades is get it:

```
git clone https://gitlab.com/WiseDragonStd/HadesWrapper
```

## Features and roadmap

- [x] Bot class
    - [x] connectToDatabase
    - [x] connectToRedis
    - [x] Api methods
        - [x] apiRequest
        - [x] getUpdates
            - [x] getUpdatesRedis
            - [x] getUpdatesDatabase
        - [x] sendMessage
            - [x] sendDefaultMessage
            - [x] sendMessageKeyboard
            - [x] sendReplyMessage
            - [x] sendReplyMessageKeyboard
        - [x] forwardMessage
        - [x] sendPhoto
        - [] sendAudio
        - [] sendDocument
        - [x] sendSticker
        - [] sendVideo
        - [] sendVoice
        - [] sendLocation
        - [] sendVenue
        - [] sendContact
        - [x] sendChatAction
        - [] getUserProfilePhotos
        - [] getFile
        - [] kickChatMember
        - [] leaveChat
        - [] unbanChatMember
        - [x] getChat
        - [] getChatAdministrators
        - [] getChatMembersCount
        - [] getChatMember
        - [x] answerCallbackQuery
            - [x] answerEmptyCallbackQuery
        - [x] editMessageText
            - [x] editInlineMessageText
            - [x] editMessageTextKeyboard
            - [x] editInlineMessageTextKeyboard
        - [] editMessageCaption
        - [x] editMessageReplyMarkup
            - [x] editInlineMessageReplyMarkup
        - [x] answerInlineQuery
            - [x] answerInlineQueryArticleSwitchPM
- [x] Inline_keyboard class
    - [x] addLevelButtons
    - [x] getKeyboard
        - [x] getNoJSONKeyboard
    - [] getListKeyboard
    - [] getBackKeyboard
- [x] Inline_query_results
    - [x] newArticle
        - [x] newArticleKeyboard
    - [] newResult
    - [x] getResults
-[x] Database
    - [x] getPDO
    - [x] createTable
    - [x] insertTable
    - [x] deleteTable
- [] Examples
    - [] Webhook
    - [] getUpdateDatabase
    - [] getUpdateRedis
    - [] configuration
- [x] Documentation
    - [x] Readme
    - [x] License
    - [x] Changelog
    - [] Function examples

## Author
This wrapper was developed by @WiseDragonStd.
- Bot, Inline_keyboard and Inline_query_results classes created by @danyspin97
- Database class created by @dom.corvasce

## [License](https://gitlab.com/WiseDragonStd/HadesWrapper/blob/master/LICENSE.md)

Hades is released under GNU GPLv3 license generally know as AGPLv3.