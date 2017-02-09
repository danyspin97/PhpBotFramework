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

trait Inline
{

    abstract protected function execRequest(string $url);

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    /** \brief Store ID of the callback query received. */
    protected $_callback_query_id;

    /** \brief Store ID of the inline query received. */
    protected $_inline_query_id;

    /** @} */

    /**
     * \addtogroup API API Methods
     * @{
     */

    /* \brief Answer a callback query
     * \details Remove the 'updating' circle icon on an inline keyboard button showing a message/alert to the user.
     * It'll always answer the current callback query.
     * @param $text <i>Optional</i>. Text of the notification. If not specified, nothing will be shown to the user, 0-200 characters.
     * @param $show_alert <i>Optional</i>. If true, an alert will be shown by the client instead of a notification at the top of the chat screen.
     * @param $url <i>Optional</i>. URL that will be opened by the user's client. If you have created a Game and accepted the conditions via @Botfather, specify the URL that opens your game – note that this will only work if the query comes from a callback_game button.
     * Otherwise, you may use links like telegram.me/your_bot?start=XXXX that open your bot with a parameter.
     * @return True on success.
     */
    public function answerCallbackQuery($text = '', $show_alert = false, string $url = '') : bool
    {

        if (!isset($this->_callback_query_id)) {
            throw new BotException("Callback query id not set, wrong update");
        }

        $parameters = [
            'callback_query_id' => $this->_callback_query_id,
            'text' => $text,
            'show_alert' => $show_alert,
            'url' => $url
        ];

        return $this->execRequest('answerCallbackQuery?' . http_build_query($parameters));
    }


    /*
     * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the
     * private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
     * @param
     * $results Array on InlineQueryResult (https://core.telegram.org/bots/api#inlinequeryresult)
     * $switch_pm_text Text to show on the button
     */
    public function answerInlineQuerySwitchPM($results, $switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300)
    {

        if (!isset($this->_inline_query_id)) {
            throw new BotException("Inline query id not set, wrong update");
        }

        $parameters = [
            'inline_query_id' => $this->_inline_query_id,
            'switch_pm_text' => $switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'results' => $results,
            'cache_time' => $cache_time
        ];

        return $this->execRequest('answerInlineQuery?' . http_build_query($parameters));
    }

    /*
     * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to
     * the private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
     * without showing any results to the user
     * @param
     * $switch_pm_text Text to show on the button
     */
    public function answerEmptyInlineQuerySwitchPM($switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300)
    {

        if (!isset($this->_inline_query_id)) {
            throw new BotException("Inline query id not set, wrong update");
        }

        $parameters = [
            'inline_query_id' => $this->_inline_query_id,
            'switch_pm_text' => $switch_pm_text,
            'is_personal' => $is_personal,
            'switch_pm_parameter' => $switch_pm_parameter,
            'cache_time' => $cache_time
        ];

        return $this->execRequest('answerInlineQuery?' . http_build_query($parameters));
    }

    /** @} */
}
