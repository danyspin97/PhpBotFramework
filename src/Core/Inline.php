<?php

namespace PhpBotFramework\Core;

trait Inline {

    abstract protected function exec_curl_request($url, $method);

    /**
     * \addtogroup Core Core(Internal)
     * @{
     */

    /** \brief Store id of the callback query received. */
    protected $_callback_query_id;

    /** \brief Store id of the inline query received. */
    protected $_inline_query_id;

    /** @} */

    /**
     * \addtogroup Api Api Methods
     * @{
     */

    /* \brief Answer a callback query
     * \details Remove the updating cirle on an inline keyboard button and showing a message/alert to the user.
     * It will always answer the current callback query.
     * @param $text <i>Optional</i>. Text of the notification. If not specified, nothing will be shown to the user, 0-200 characters.
     * @param $show_alert <i>Optional</i>. If true, an alert will be shown by the client instead of a notification at the top of the chat screen.
     * @param $url <i>Optional</i>. URL that will be opened by the user's client. If you have created a Game and accepted the conditions via @Botfather, specify the URL that opens your game – note that this will only work if the query comes from a callback_game button.
     * Otherwise, you may use links like telegram.me/your_bot?start=XXXX that open your bot with a parameter.
     * @return True on success.
     */
    public function answerCallbackQuery($text = '', $show_alert = false, string $url = '') : bool {

        if (!isset($this->_callback_query_id)) {

            throw new BotException("Callback query id not set, wrong update");

        }

        $parameters = [
            'callback_query_id' => $this->_callback_query_id,
            'text' => $text,
            'show_alert' => $show_alert,
            'url' => $url
        ];

        return $this->exec_curl_request('answerCallbackQuery?' . http_build_query($parameters));

    }


    /*
     * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
     * @param
     * $results Array on InlineQueryResult (https://core.telegram.org/bots/api#inlinequeryresult)
     * $switch_pm_text Text to show on the button
     */
    public function answerInlineQuerySwitchPM($results, $switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {

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

        return $this->exec_curl_request('answerInlineQuery?' . http_build_query($parameters));

    }

    /*
     * Answer a inline query (when the user write @botusername "Query") with a button, that will make user switch to the private chat with the bot, on the top of the results (https://core.telegram.org/bots/api#answerinlinequery)
     * without showing any results to the user
     * @param
     * $switch_pm_text Text to show on the button
     */
    public function answerEmptyInlineQuerySwitchPM($switch_pm_text, $switch_pm_parameter = '', $is_personal = true, $cache_time = 300) {

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

        return $this->exec_curl_request('answerInlineQuery?' . http_build_query($parameters));

    }

    /** @} */

}
