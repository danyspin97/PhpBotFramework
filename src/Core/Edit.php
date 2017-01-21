<?php

namespace PhpBotFramework\Core;

trait Edit {

    /**
     * \addtogroup Api Api Methods
     * @{
     */

    /**
     * \brief Edit text of a message sent by the bot.
     * \details Use this method to edit text and game messages sent by the bot. [Api reference](https://core.telegram.org/bots/api#editmessagetext)
     * @param $message_id Unique identifier of the sent message.
     * @param $text New text of the message.
     * @param $reply_markup Reply markup of the message will have (will be removed if this is null).
     * @param $parse_mode <i>Optional</i>. Send Markdown or HTML.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     */
    public function editMessageText(int $message_id, $text, $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = true) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
        ];

        return $this->exec_curl_request('editMessageText?' . http_build_query($parameters));

    }

    /**
     * \brief Edit text of a message sent via the bot.
     * \details Use this method to edit text messages sent via the bot (for inline queries). [Api reference](https://core.telegram.org/bots/api#editmessagetext)
     * @param $inline_message_id  Identifier of the inline message.
     * @param $text New text of the message.
     * @param $reply_markup Reply markup of the message will have (will be removed if this is null).
     * @param $parse_mode <i>Optional</i>. Send Markdown or HTML.
     * @param $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     */
    public function editInlineMessageText(string $inline_message_id, $text, string $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = false) {

        $parameters = [
            'inline_message_id' => $inline_message_id,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
        ];

        return $this->exec_curl_request('editMessageText?' . http_build_query($parameters));

    }

    /*
     * Edit only the inline keyboard of a message (https://core.telegram.org/bots/api#editmessagereplymarkup)ù
     * @param
     * $message_id Identifier of the message to edit
     * $inline_keyboard Inlike keyboard array (https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     */
    public function editMessageReplyMarkup($message_id, $inline_keyboard) {

        $parameters = [
            'chat_id' => $this->_chat_id,
            'message_id' => $message_id,
            'reply_markup' => $inline_keyboard,
        ];

        return $this->exec_curl_request('editMessageReplyMarkup?' . http_build_query($parameters));

    }

    /** @} */

}
