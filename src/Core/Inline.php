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

use PhpBotFramework\Exceptions\BotException;

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

    /** \brief Answer a callback query.
     * \details Remove the 'updating' circle icon on an inline keyboard button showing a message/alert to the user.
     * It'll always answer the current callback query. [Api reference](https://core.telegram.org/bots/api#answercallbackquery)
     * @param string $text <i>Optional</i>. Text of the notification. If not specified, nothing will be shown to the user, 0-200 characters.
     * @param bool $show_alert <i>Optional</i>. If true, an alert will be shown by the client instead of a notification at the top of the chat screen.
     * @param string $url <i>Optional</i>. URL that will be opened by the user's client. If you have created a Game and accepted the conditions via @Botfather, specify the URL that opens your game – note that this will only work if the query comes from a callback_game button.
     * Otherwise, you may use links like telegram.me/your_bot?start=XXXX that open your bot with a parameter.
     * @return bool True on success.
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


    /**
     * \brief Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the
     * private chat with the bot, on the top of the results. [Api reference](https://core.telegram.org/bots/api#answerinlinequery)
     * @param string $results A JSON-serialized array of results for the inline query. Use PhpBotFramework\Entities\InlineQueryResult class to create and get them.
     * @param string $switch_pm_text If passed, clients will display a button with specified text that switches the user to a private chat with the bot and sends the bot a start message with the parameter $switch_pm_parameter.
     * @param bool $is_personal Pass True, if results may be cached on the server side only for the user that sent the query. By default, results may be returned to any user who sends the same query.
     * @param int $cache_time The maximum amount of time in seconds that the result of the inline query may be cached on the server. Defaults to 300.
     * @return bool True on success.
     */
    public function answerInlineQuery(string $results = '', string $switch_pm_text = '', $switch_pm_parameter = '', bool $is_personal = true, int $cache_time = 300) : bool
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

        if (isset($results) && $results !== '') {
            $parameters['results'] = $results;
        }

        return $this->execRequest('answerInlineQuery?' . http_build_query($parameters));
    }

    /** @} */
}
