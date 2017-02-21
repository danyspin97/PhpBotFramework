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

trait Edit
{
    abstract protected function execRequest(string $url);

    /** \brief Contains parameters of the next request. */
    protected $parameters;

    /**
     * \addtogroup Api Api Methods
     * @{
     */

    /**
     * \brief Edit text of a message sent by the bot.
     * \details Use this method to edit text and game messages sent by the bot. [API reference](https://core.telegram.org/bots/api#editmessagetext)
     * @param int $message_id Unique identifier of the sent message.
     * @param string $text New text of the message.
     * @param string $reply_markup Reply markup of the message will have (will be removed if this is null).
     * @param string $parse_mode <i>Optional</i>. Send Markdown or HTML.
     * @param bool $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     * @return Message|false Message edited, false otherwise.
     */
    public function editMessageText(int $message_id, string $text, string $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = true)
    {

        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
        ];

        return $this->processRequest('editMessageText', 'Message');
    }

    /**
     * \brief Edit text of a message sent via the bot.
     * \details Use this method to edit text messages sent via the bot (for inline queries). [API reference](https://core.telegram.org/bots/api#editmessagetext)
     * @param string $inline_message_id  Identifier of the inline message.
     * @param string $text New text of the message.
     * @param string $reply_markup Reply markup of the message will have (will be removed if this is null).
     * @param string $parse_mode <i>Optional</i>. Send Markdown or HTML.
     * @param bool $disable_web_preview <i>Optional</i>. Disables link previews for links in this message.
     * @return bool True on success.
     */
    public function editInlineMessageText(string $inline_message_id, string $text, string $reply_markup = null, string $parse_mode = 'HTML', bool $disable_web_preview = false) : bool
    {

        $parameters = [
            'inline_message_id' => $inline_message_id,
            'text' => $text,
            'reply_markup' => $reply_markup,
            'parse_mode' => $parse_mode,
            'disable_web_page_preview' => $disable_web_preview,
        ];

        return $this->execRequest('editMessageText?' . http_build_query($parameters));
    }

    /**
     * \brief Edit only the inline keyboard of a message.
     * \details[API reference](https://core.telegram.org/bots/api#editmessagereplymarkup)
     * @param int $message_id Identifier of the message to edit
     * @param string $inline_keyboard Inlike keyboard array. [API reference](https://core.telegram.org/bots/api#inlinekeyboardmarkup)
     * @return Message|false Message edited, false otherwise.
     */
    public function editMessageReplyMarkup(int $message_id, string $inline_keyboard)
    {

        $this->parameters = [
            'chat_id' => $this->_chat_id,
            'message_id' => $message_id,
            'reply_markup' => $inline_keyboard,
        ];

        return $this->processRequest('editMessageReplyMarkup', 'Message');
    }

    /** @} */
}
