# v2.0.0
- Reworked all framework
- Splited Bot and CoreBot classes in different classes and traits to improve modularity
- Improved multi language support by adding getStr method to get a localized string
- Save all localized strings in json files and load them using loadAllLanguages or loadSingleLanguage methods
- Database handling has been improved by adding some methods (connect, addUser)
- Broadcast a message to all bot users
- Redis cannot be used anymore as stand alone database for containing user languages
- Send file along with api methods whetever they are, they will be handled automatically
- Upload files along with api request, send local image as photo just by sending its local path
- Added PHPStan as checker to fix majority of bugs
- Improved design by appling "composition over inheritance" principle
- Improved documentation
- Added simple unit testing
- Added variuos example
- Fixed many bugs

# v1.0.0
- Reworked processUpdate
- Added command-handle system
- Removed optimized api methods
- Added doxygen documentation

## v0.3.6
- Added try/cath in adjustOffsetRedis

## v0.3.5
- Added getUpdatesLocal and getNextUpdates functions

## v0.3.4
- Added basic keyboards
- Added adjustOffsetRedis and adjustOffsetDatabase
- Added templates

## v0.3.3
- Changed namespace to DanySpin97\\PhpBotFramework\\
- Bug fixes

## v0.3.2
- Split Bot class in CoreBot and Bot classes
- Added clearKeyboard method to Inline_keyboard
- Fixed bugs and indentation

## v0.3.1
- Added Continuos Integration
- Added PhpUnit test
- Fixed indentation errors

# v0.3.0
- Numerous bug fixes
- Deleted some Bot methods
- Added Changelog
- Added _Features and roadmap_ and _Authors_ to Readme
- Updated composer.json
- Removed _test_ folder
