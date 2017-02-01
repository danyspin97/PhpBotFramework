<?php

namespace PhpBotFramework\Core;

use PhpBotFramework\Entities\Message;

trait Send {

    abstract protected function execRequest(string $url, string $method);

    abstract protected function processRequest(string $method, array $param, string $class);

    /**
     * \addtogroup Api Api Methods
     * @{
     */

    /**
     * \brief Send a text message.
     * \details Use this method to send text messages. [Api reference](https://core.telegram.org/bots/api#sendmessage)
     * @param $text Text of the message.
     * @param $reply_markup <i>Optional</i>. Reply_markup of the message.
     * @param $parse_mode <i>Optional</i>. Parse mode of the message.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendMessage($text, string $reply_markup = null, int $reply_to = null, string $parse_mode = 'HTML', bool $disable_web_preview = true, bool $disable_notification = false) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'text' => $text,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
            'reply_markup' => $reply_markup,
            'reply_to_message_id' => $reply_to,
            'disable_notification' => $disable_notification
        ];

        return $this->processRequest('sendMessage', $parameters, 'Message');

    }

    /**
     * \brief Forward a message.
     * \details Use this method to forward messages of any kind. [Api reference](https://core.telegram.org/bots/api#forwardmessage)
     * @param $from_chat_id The chat where the original message was sent.
     * @param $message_id Message identifier (id).
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function forwardMessage($from_chat_id, int $message_id, bool $disable_notification = false) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'message_id' => $message_id,
            'from_chat_id' => $from_chat_id,
            'disable_notification' => $disable_notification
        ];

        return $this->processRequest('forwardMessage', $parameters, 'Message');

    }

    /**
     * \brief Send a photo.
     * \details Use this method to send photos. [Api reference](https://core.telegram.org/bots/api#sendphoto)
     * @param $photo Photo to send, can be a file_id or a string referencing the location of that image.
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param $caption <i>Optional</i>. Photo caption (may also be used when resending photos by file_id), 0-200 characters.
     * @param $disable_notification <i>Optional<i>. Sends the message silently.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendPhoto($photo, string $reply_markup = null, string $caption = '', bool $disable_notification = false) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->processRequest('sendPhoto', $parameters, 'Message');

    }

    /**
     * \brief Send an audio.
     * \details Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .mp3 format. [Api reference](https://core.telegram.org/bots/api/#sendaudio)
     * Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
     * @param $audio Audio file to send. Pass a file_id as String to send an audio file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get an audio file from the Internet, or upload a new one using multipart/form-data.
     * @param $caption <i>Optional</i>. Audio caption, 0-200 characters.
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param $duration <i>Optional</i>. Duration of the audio in seconds.
     * @param $performer <i>Optional</i>. Performer.
     * @param $title <i>Optional</i>. Track name.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @param $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendAudio($audio, string $caption = null, string $reply_markup = null, int $duration = null, string $performer, string $title = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'audio' => $audio,
            'caption' => $caption,
            'duration' => $duration,
            'performer' => $performer,
            'title' => $title,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->processRequest('sendAudio', $parameters, 'Message');

    }

    /**
     * \brief Send a document.
     * \details Use this method to send general files. [Api reference](https://core.telegram.org/bots/api/#senddocument)
     * @param mixed $document File to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data.
     * @param string $caption <i>Optional</i>. Document caption (may also be used when resending documents by file_id), 0-200 characters.
     *
     * @param string $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param bool $disable_notification <i>Optional</i>. Sends the message silently.
     * @param int $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendDocument($document, string $caption = '', string $reply_markup = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'document' => $document,
            'caption' => $caption,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->processRequest('sendDocument', $parameters, 'Message');

    }


    /**
     * \brief Send a sticker
     * \details Use this method to send .webp stickers. [Api reference](https://core.telegram.org/bots/api/#sendsticker)
     * @param mixed $sticker Sticker to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a .webp file from the Internet, or upload a new one using multipart/form-data.
     * @param string $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param bool $disable_notification Sends the message silently.
     * @param int $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @param bool On success, the sent message.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendSticker($sticker, string $reply_markup = null, bool $disable_notification = false, int $reply_to_message_id = null) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'sticker' => $sticker,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ];

        return $this->processRequest('sendSticker', $parameters, 'Message');

    }

    /**
     * \brief Send audio files.
     * \details Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message. For this to work, your audio must be in an .ogg file encoded with OPUS (other formats may be sent as Audio or Document).o
     * Bots can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
     * @param mixed $voice Audio file to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or upload a new one using multipart/form-data.
     * @param string $caption <i>Optional</i>. Voice message caption, 0-200 characters
     * @param int $duration <i>Optional</i>. Duration of the voice message in seconds
     * @param string $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param bool $disable_notification <i>Optional</i>. Sends the message silently.
     * @param int $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendVoice($voice, string $caption, int $duration, string $reply_markup = null, bool $disable_notification, int $reply_to_message_id = 0) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'voice' => $voice,
            'caption' => $caption,
            'duration' => $duration,
            'disable_notification', $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ];

        return $this->processRequest('sendVoice', $parameters, 'Message');

    }

    /**
     * \brief Say the user what action is the bot doing.
     * \details Use this method when you need to tell the user that something is happening on the bot's side. The status is set for 5 seconds or less (when a message arrives from your bot, Telegram clients clear its typing status). [Api reference](https://core.telegram.org/bots/api#sendchataction)
     * @param string $action Type of action to broadcast. Choose one, depending on what the user is about to receive:
     * - <code>typing</code> for text messages
     * - <code>upload_photo</code> for photos
     * - <code>record_video</code> or <code>upload_video</code> for videos
     * - <code>record_audio</code> or <code>upload_audio</code> for audio files
     * - <code>upload_document</code> for general files
     * - <code>find_location</code> for location data
     * @return bool True on success.
     */
    public function sendChatAction(string $action) : bool {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'action' => $action
        ];

        return $this->execRequest('sendChatAction?' . http_build_query($parameters));

    }

    /** @} */

}
