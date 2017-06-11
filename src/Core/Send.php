<?php

/*
 * This file is part of the PhpBotFramework.
 *
 * PhpBotFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * PhpBotFramework is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PhpBotFramework\Core;

use PhpBotFramework\Entities\Message;

use PhpBotFramework\Entities\File as TelegramFile;

/**
 * \class Send
 * \brief All API Methods that send a message (text based or not).
 */
trait Send
{
    abstract protected function execRequest(string $url);

    abstract protected function processRequest(string $method, string $class, $file);

    abstract protected function checkCurrentFile(TelegramFile $file);

    /** @internal
      * \brief Contains parameters of the next request. */
    protected $parameters;

    /** @internal
     * \brief Represents the provider token for payments. */
    protected $_provider_token;

    /** @internal
     * \brief Represents currency used for payments. */
    protected $_payment_currency;

    /**
     * \addtogroup Api Api Methods
     * @{
     */

    /**
     * \brief Set data for bot payments used across 'sendInvoice'.
     * @param string $provider_token The token for the payment provider got using BotFather.
     * @param string $currency The payment currency (represented with 'ISO 4217 currency mode').
     */
    public function setPayment(string $provider_token, string $currency = 'EUR')
    {
        $this->_provider_token = $provider_token;
        $this->_payment_currency = $currency;
    }

    /**
     * \brief Set currency for bot payments.
     * \details It's used in place of 'setPayment' in order to specify only the currency.
     * @param string $currency The payment currency (represented with 'ISO 4217 currency mode').
     */
    public function setPaymentCurrency(string $currency = 'EUR')
    {
        $this->_payment_currency = $currency;
    }

    /**
     * \brief Send an invoice.
     * \details Send an invoice in order receive real money. [API reference](https://core.telegram.org/bots/api#sendinvoice).
     * @param $title The title of product or service to pay (e.g. Free Donation to Telegram).
     * @param $description A description of product or service to pay.
     * @param $payload Bot-defined invoice payload.
     * @param $start_parameter Unique deep-linking parameter used to generate this invoice.
     * @param $prices The various prices to pay (e.g array('Donation' => 14.50, 'Taxes' => 0.50)).
     * @return string The payload for that specific invoice.
     * @return Message|false message sent on success, false otherwise.
     */
    public function sendInvoice(string $title, string $description, string $start_parameter, $prices) {
      $payload = $this->generateSecurePayload();

      $this->parameters = [
        'chat_id' => $this->_chat_id,
        'title' => $title,
        'description' => $description,
        'payload' => $payload,
        'provider_token' => $this->_provider_token,
        'start_parameter' => $start_parameter,
        'currency' => $this->_payment_currency,
        'prices' => $this->generateLabeledPrices($prices)
      ];

      return [$payload, $this->processRequest('sendInvoice', 'Message')];
    }

    /**
     * \brief Generate a secure and unique payload string.
     * @return string The generated payload.
     */
    private function generateSecurePayload()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * \brief Convert a matrix of prices in a JSON string object accepted by 'sendInvoice'.
     * @param $prices The matrix of prices.
     * @return string The JSON string response.
     */
    private function generateLabeledPrices(array $prices) {
      $response = [];

      foreach ($prices as $item => $price) {
        if ($price < 0) {
          throw new \Exception('Invalid negative price passed to "sendInvoice"');
        }

        // Format the price value following the official guideline:
        // https://core.telegram.org/bots/api#labeledprice
        $formatted_price = intval($price * 100);
        array_push($response, ['label' => $item, 'amount' => $formatted_price]);
      }

      return json_encode($response);
    }

    /**
     * \brief Send a text message.
     * \details Use this method to send text messages. [API reference](https://core.telegram.org/bots/api#sendmessage)
     * @param $text Text of the message.
     * @param $reply_markup <i>Optional</i>. Reply_markup of the message.
     * @param $parse_mode <i>Optional</i>. Parse mode of the message.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendMessage($text, string $reply_markup = null, int $reply_to = null, string $parse_mode = 'HTML', bool $disable_web_preview = true, bool $disable_notification = false)
    {
        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'text' => $text,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
            'reply_markup' => $reply_markup,
            'reply_to_message_id' => $reply_to,
            'disable_notification' => $disable_notification
        ];

        return $this->processRequest('sendMessage', 'Message');
    }

    /**
     * \brief Forward a message.
     * \details Use this method to forward messages of any kind. [API reference](https://core.telegram.org/bots/api#forwardmessage)
     * @param $from_chat_id The chat where the original message was sent.
     * @param $message_id Message identifier (id).
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function forwardMessage($from_chat_id, int $message_id, bool $disable_notification = false)
    {
        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'message_id' => $message_id,
            'from_chat_id' => $from_chat_id,
            'disable_notification' => $disable_notification
        ];

        return $this->processRequest('forwardMessage', 'Message');
    }

    /**
     * \brief Send a photo.
     * \details Use this method to send photos. [API reference](https://core.telegram.org/bots/api#sendphoto)
     * @param $photo Photo to send, can be a file_id or a string referencing the location of that image(both local or remote path).
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param $caption <i>Optional</i>. Photo caption (may also be used when resending photos by file_id), 0-200 characters.
     * @param $disable_notification <i>Optional<i>. Sends the message silently.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendPhoto(&$photo, string $reply_markup = null, string $caption = '', bool $disable_notification = false)
    {
        $this->_file->init($photo, 'photo');

        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'caption' => $caption,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->processRequest('sendPhoto', 'Message', $this->checkCurrentFile());
    }

    /**
     * \brief Send an audio.
     * \details Use this method to send audio files, if you want Telegram clients to display them in the music player. Your audio must be in the .mp3 format. [API reference](https://core.telegram.org/bots/api/#sendaudio)
     * Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
     * @param $audio Audio file to send. Pass a file_id as String to send an audio file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get an audio file from the Internet, or give the local path of an audio to upload.
     * @param $caption <i>Optional</i>. Audio caption, 0-200 characters.
     * @param $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param $duration <i>Optional</i>. Duration of the audio in seconds.
     * @param $performer <i>Optional</i>. Performer.
     * @param $title <i>Optional</i>. Track name.
     * @param $disable_notification <i>Optional</i>. Sends the message silently.
     * @param $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendAudio($audio, string $caption = null, string $reply_markup = null, int $duration = null, string $performer, string $title = null, bool $disable_notification = false, int $reply_to_message_id = null)
    {
        $this->_file->init($audio, 'audio');

        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'caption' => $caption,
            'duration' => $duration,
            'performer' => $performer,
            'title' => $title,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->processRequest('sendAudio', 'Message', $this->checkCurrentFile());
    }

    /**
     * \brief Send a document.
     * \details Use this method to send general files. [API reference](https://core.telegram.org/bots/api/#senddocument)
     * @param string $document File to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or give the local path of an document to upload it.
     * Only some document are allowed through url sending (this is a Telegram limitation).
     * @param string $caption <i>Optional</i>. Document caption (may also be used when resending documents by file_id), 0-200 characters.
     *
     * @param string $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param bool $disable_notification <i>Optional</i>. Sends the message silently.
     * @param int $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendDocument(string $document, string $caption = '', string $reply_markup = null, bool $disable_notification = false, int $reply_to_message_id = null)
    {
        $this->_file->init($document, 'document');

        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'caption' => $caption,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup,
            'disable_notification' => $disable_notification,
        ];

        return $this->processRequest('sendDocument', 'Message', $this->checkCurrentFile());
    }


    /**
     * \brief Send a sticker
     * \details Use this method to send .webp stickers. [API reference](https://core.telegram.org/bots/api/#sendsticker)
     * @param mixed $sticker Sticker to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a .webp file from the Internet, or upload a new one using multipart/form-data.
     * @param string $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param bool $disable_notification Sends the message silently.
     * @param int $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @param bool On success, the sent message.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendSticker($sticker, string $reply_markup = null, bool $disable_notification = false, int $reply_to_message_id = null)
    {
        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'sticker' => $sticker,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ];

        return $this->processRequest('sendSticker', 'Message');
    }

    /**
     * \brief Send audio files.
     * \details Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message. For this to work, your audio must be in an .ogg file encoded with OPUS (other formats may be sent as Audio or Document).o
     * Bots can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
     * @param mixed $voice Audio file to send. Pass a file_id as String to send a file that exists on the Telegram servers (recommended), pass an HTTP URL as a String for Telegram to get a file from the Internet, or the local path of a voice to upload.
     * @param string $caption <i>Optional</i>. Voice message caption, 0-200 characters
     * @param int $duration <i>Optional</i>. Duration of the voice message in seconds
     * @param string $reply_markup <i>Optional</i>. Reply markup of the message.
     * @param bool $disable_notification <i>Optional</i>. Sends the message silently.
     * @param int $reply_to_message_id <i>Optional</i>. If the message is a reply, ID of the original message.
     * @return Message|false Message sent on success, false otherwise.
     */
    public function sendVoice($voice, string $caption, int $duration, string $reply_markup = null, bool $disable_notification, int $reply_to_message_id = 0)
    {
        $this->_file->init($voice, 'voice');

        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'caption' => $caption,
            'duration' => $duration,
            'disable_notification', $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
            'reply_markup' => $reply_markup
        ];

        return $this->processRequest('sendVoice', 'Message', $this->checkCurrentFile());
    }

    /**
     * \brief Say the user what action bot's going to do.
     * \details Use this method when you need to tell the user that something is happening on the bot's side.
     * The status is set for 5 seconds or less (when a message arrives from your bot,
     * Telegram clients clear its typing status). [API reference](https://core.telegram.org/bots/api#sendchataction)
     *
     * @param string $action Type of action to broadcast. Choose one, depending on what the user is about to receive:
     * - <code>typing</code> for text messages
     * - <code>upload_photo</code> for photos
     * - <code>record_video</code> or <code>upload_video</code> for videos
     * - <code>record_audio</code> or <code>upload_audio</code> for audio files
     * - <code>upload_document</code> for general files
     * - <code>find_location</code> for location data
     * @return bool True on success.
     */
    public function sendChatAction(string $action) : bool
    {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'action' => $action
        ];

        return $this->execRequest('sendChatAction?' . http_build_query($parameters));
    }

    /** @} */
}
