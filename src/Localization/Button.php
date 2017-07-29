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

namespace PhpBotFramework\Localization;

use PhpBotFramework\Core\CoreBot;
use PhpBotFramework\Entities\InlineKeyboard;

/** \class Button Button Inline keyboard with localizated buttons. */
class Button extends InlineKeyboard
{
    /**
     * \addtogroup InlineKeyboard InlineKeyboard
     * \brief Handle an inline keyboard to send along with messages.
     * @{
     */

    /** \brief Stores a reference to the bot that's using the inline keyboard. */
    protected $bot;

    /**
     * \brief Create an inline keyboard object with localizated buttons.
     * @param CoreBot $bot References to the bot that's using the inline keyboard.
     * @param array $buttons Buttons passed as inizialization.
     */
    public function __construct(CoreBot &$bot, array $buttons = array())
    {
        $this->bot = $bot;

        // Call parent constructor passing array
        parent::__construct($buttons);
    }


    /**
     * \brief Get a simple Back button with back as <code>callback_data</code>.
     * @param $json_serialized return a json serialized string, or an array.
     * @return string|array A button with written "back".
     */
    public function getBackButton(bool $json_serialized = true)
    {
        // Create the button
        $inline_keyboard = [ 'inline_keyboard' =>
            [
                [
                    [
                        'text' => $this->bot->local->getStr('Back_Button'),
                        'callback_data' => 'back'
                    ]
                ]
            ]
        ];

        // Serialize everything as JSON if necessary
        if ($json_serialized) {
            return json_encode($inline_keyboard);
        } else {
            return $inline_keyboard;
        }
    }

    /**
     * \brief Get a Back and a Skip buttons inthe same row.
     * \details Back button has callback_data "back" and Skip button has callback_data "skip".
     * @param bool $json_serialized return a json serialized string, or an array.
     * @return string|array A button with written "back" and one with written "Skip".
     */
    public function getBackSkipKeyboard(bool $json_serialized = true)
    {
        // Create the keyboard
        $inline_keyboard = [ 'inline_keyboard' =>
            [
                [
                    [
                        'text' => $this->bot->local->getStr('Back_Button'),
                        'callback_data' => 'back'
                    ],
                    [
                        'text' => $this->bot->local->getStr('Skip_Button'),
                        'callback_data' => 'skip'
                    ]
                ]
            ]
        ];

        if ($json_serialized) {
            return json_encode($inline_keyboard);
        } else {
            return $inline_keyboard;
        }
    }

    /**
     * \brief Get various buttons for set various languages.
     * \details Create a button for each language contained in <code>$localization['languages']</code> variable of $bot object.
     * There will be a button per row.
     *
     * The button's label will include the target language and the current one.
     * The callback data for each button will be "cl/key" where key is the key in <code>$localization['languages']</code>.
     * @param $prefix Prefix followed by '/' and the language index (en, it..).
     * @param $json_serialized Get a JSON-serialized string or an array.
     * @return string|array The buttons in the selected type.
     */
    public function getChooseLanguageKeyboard(string $prefix = 'cl', bool $json_serialized = true)
    {
        $inline_keyboard = ['inline_keyboard' => array()];

        foreach ($this->bot->local as $languages => $language_msg) {
            // If the language is the same as the one set for the current user in $bot
            if (strpos($languages, $this->bot->local->language) !== false) {
                // Just create a button with one language in it
                array_push($inline_keyboard['inline_keyboard'], [
                    [
                        'text' => $language_msg['Language'],
                        'callback_data' => 'same/language'
                    ]
                ]);
            } else {
                array_push($inline_keyboard['inline_keyboard'], [
                        [
                            'text' => $language_msg['Language'] . '/' . $this->bot->local[$this->bot->local->language][$languages],
                            'callback_data' => $prefix . '/' . $languages
                        ]
                ]);
            }
        }

        // Unset the variables from the foreach
        unset($languages);
        unset($language_msg);

        array_push($inline_keyboard['inline_keyboard'], [
                [
                    'text' => $this->bot->local->getStr('Back_Button'),
                    'callback_data' => 'back'
                ]
        ]);

        if ($json_serialized) {
            return json_encode($inline_keyboard);
        } else {
            return $inline_keyboard;
        }
    }

    /** @} */
}
