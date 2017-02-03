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

namespace PhpBotFramework\Entities;

use PhpBotFramework\Exceptions\BotException;

use PhpBotFramework\Localization\Button;

/**
 * \addtogroup Entities Entities
 * @{
 */

/** \class InlineKeyboard
 * \brief Inline Keyboard handler that create and handle inline keyboard buttons.
 * \details It stores the inline keyboard buttons added until get() is called.
 * It also provides some basic button to get, like Menu and Back buttons plus the dynamic-keyboard for menu browsing.
 */
class InlineKeyboard
{
    use Button;

    /**
     * \addtogroup InlineKeyboard InlineKeyboard
     * \brief Handle an inline keyboard to send along with messages.
     * @{
     */

    /** \brief Store the array of InlineKeyboardButton */
    protected $inline_keyboard;

    /** \brief Store the current row. */
    private $row;

    /** \brief Store the current column. */
    private $column;

    /**
     * \brief Create an inline keyboard object.
     * @param array $buttons Buttons passed as inizialization.
     */
    public function __construct(
        array $buttons = array()
    ) {
        // If $buttons is empty, initialize it with an empty array
        $this->inline_keyboard = $buttons;

        // Set up vars
        $this->row = 0;
        $this->column = 0;
    }

    /**
     * \brief Get a JSON-serialized object containg the inline keyboard.
     * @param bool $clear_keyboard Remove all the buttons from this object.
     * @return string JSON-serialized string with the buttons.
     */
    public function get(bool $clear_keyboard = true) : string
    {
        // Check if it is empty
        if (empty($this->inline_keyboard)) {
            throw new BotException("Inline keyboard is empty");
        }

        // Create a new array to put our buttons
        $reply_markup = ['inline_keyboard' => $this->inline_keyboard];

        // Encode the array in a json object
        $reply_markup = json_encode($reply_markup);

        if ($clear_keyboard) {
            $this->clearKeyboard();
        }

        return $reply_markup;
    }

    /**
     * \brief Get the array containing the buttons. (Use this method when adding keyboard to inline query results)
     * @param bool $clean_keyboard Remove all the button from this object.
     * @return array An array containing the buttons.
     */
    public function getArray(bool $clean_keyboard = true) : array
    {
        // Check if it is empty
        if (empty($this->inline_keyboard)) {
            throw new BotException("Inline keyboard is empty");
        }

        // Create a new array to put the buttons
        $reply_markup = ['inline_keyboard' => $this->inline_keyboard];

        if ($clean_keyboard) {
            $this->clearKeyboard();
        }

        return $reply_markup;
    }

    /** \brief Add buttons for the current row of buttons
     * \details Each array sent as parameter require a text key
     * and one another key (as specified <a href="https://core.telegram.org/bots/api/#inlinekeyboardbutton" target="blank">here</a> between:
     * - url
     * - callback_data
     * - switch_inline_query
     * - switch_inline_query_current_chat
     * - callback_game
     *
     * Each call to this function add one or more button to a row. The next call add buttons on the next row.
     * Each row allows 8 buttons per row and 12 columns total.
     * Use this function with this syntax:
     *
     *     addLevelButtons(['text' => 'Click me!', 'url' => 'https://telegram.me']);
     *
     * If you want to add more than a button, use this syntax:
     *
     *     addLevelButtons(['text' => 'Button 1', 'url' => 'https://telegram.me/gamedev_ita'], ['text' => 'Button 2', 'url' => 'https://telegram.me/animewallpaper']);
     *
     * @param array ...$buttons One or more arrays, each one represent a button.
     */
    public function addLevelButtons(array ...$buttons)
    {
        // If the user has already added a button in this row
        if ($this->column != 0) {
             // Change row
             $this->changeRow();
        }

        // Add buttons to the next row
        $this->inline_keyboard[] = $buttons;

        // Switch to the next row
        $this->changeRow();
    }

    /** \brief Add a button.
     * \details The button will be added next to the last one or in the next row if the bot has reached the limit for the buttons per row.
     *
     * Each row allows 8 buttons per row and 12 columns total.
     * Use this function with this syntax:
     *
     *     addButton('Click me!', 'url', 'https://telegram.me');
     *
     * @param string $text Text showed on the button.
     * @param string $data_type The type of the button data.
     * Select one from these types.
     * - url
     * - callback_data
     * - switch_inline_query
     * - switch_inline_query_current_chat
     * - callback_game
     * @param string $data Data for the type selected.
     */
    public function addButton(string $text, string $data_type, string $data)
    {
        // If we get the end of the row
        if ($this->column == 8) {
            $this->changeRow();
        }

        // Add the button
        $this->inline_keyboard[$this->row][$this->column] = ['text' => $text, $data_type => $data];

        // Update column
        $this->column++;
    }

    /**
     * \brief Change row for the current keyboard.
     * \details Buttons will be added in the next row from now on (until next InlineKeyboard::addLevelButtons() or InlineKeyboard::changeRow() call or InlineKeyboard::addButton() reaches the max).
     */
    public function changeRow()
    {

        // Reset vars
        $this->row++;
        $this->column = 0;
    }

    /** \brief Remove all the buttons from the current inline keyboard. */
    public function clearKeyboard()
    {
        // Set the inline keyboard to an empty array
        $this->inline_keyboard = [];

        // Reset vars
        $this->row = 0;
        $this->column = 0;
    }

    /**
     * \brief */
    public function addListKeyboard(int $index, int $list, $prefix = 'list')
    {
        if (($list > 0) && ($index >= 0)) {
            if ($index == 0) {
                if ($list > 1) {
                    if ($list > 2) {
                        if ($list > 3) {
                            if ($list > 4) {
                                if ($list > 5) {
                                    $buttons = [
                                            [
                                                'text' => '1',
                                                'callback_data' => $prefix . '/1'
                                            ],
                                            [
                                                'text' => '2',
                                                'callback_data' => $prefix . '/2'
                                            ],
                                            [
                                                'text' => '3',
                                                'callback_data' => $prefix . '/3'
                                            ],
                                            [
                                                'text' => '4 ›',
                                                'callback_data' => $prefix . '/4'
                                            ],
                                            [
                                                'text' => "$list ››",
                                                'callback_data' => $prefix . "/$list"
                                            ]
                                        ];
                                } else {
                                    $buttons = [
                                            [
                                                'text' => '1',
                                                'callback_data' => $prefix . '/1'
                                            ],
                                            [
                                                'text' => '2',
                                                'callback_data' => $prefix . '/2'
                                            ],
                                            [
                                                'text' => '3',
                                                'callback_data' => $prefix . '/3'
                                            ],
                                            [
                                                'text' => '4',
                                                'callback_data' => $prefix . '/4'
                                            ],
                                            [
                                                'text' => '5',
                                                'callback_data' => $prefix . '/5'
                                            ]
                                        ];
                                }
                            } else {
                                $buttons = [
                                        [
                                            'text' => '1',
                                            'callback_data' => $prefix . '/1'
                                        ],
                                        [
                                            'text' => '2',
                                            'callback_data' => $prefix . '/2'
                                        ],
                                        [
                                            'text' => '3',
                                            'callback_data' => $prefix . '/3'
                                        ],
                                        [
                                            'text' => '4',
                                            'callback_data' => $prefix . '/4'
                                        ],
                                    ];
                            }
                        } else {
                            $buttons = [
                                    [
                                        'text' => '1',
                                        'callback_data' => $prefix . '/1'
                                    ],
                                    [
                                        'text' => '2',
                                        'callback_data' => $prefix . '/2'
                                    ],
                                    [
                                        'text' => '3',
                                        'callback_data' => $prefix . '/3'
                                    ],
                                ];
                        }
                    } elseif ($list == 2) {
                        $buttons = [
                                [
                                    'text' => '1',
                                    'callback_data' => $prefix . '/1'
                                ],
                                [
                                    'text' => '2',
                                    'callback_data' => $prefix . '/2'
                                ],
                            ];
                    }
                } else {
                    $buttons = [
                            [
                                'text' => '1',
                                'callback_data' => $prefix . '/1'
                            ]
                    ];
                }
            } elseif ($index == 1) {
                if ($list > 1) {
                    if ($list > 2) {
                        if ($list > 3) {
                            if ($list > 4) {
                                if ($list > 5) {
                                    $buttons = [
                                            [
                                                'text' => '• 1 •',
                                                'callback_data' => 'null'
                                            ],
                                            [
                                                'text' => '2',
                                                'callback_data' => $prefix . '/2'
                                            ],
                                            [
                                                'text' => '3',
                                                'callback_data' => $prefix . '/3'
                                            ],
                                            [
                                                'text' => '4 ›',
                                                'callback_data' => $prefix . '/4'
                                            ],
                                            [
                                                'text' => "$list ››",
                                                'callback_data' => $prefix . "/$list"
                                            ]
                                        ];
                                } else {
                                    $buttons = [
                                            [
                                                'text' => '• 1 •',
                                                'callback_data' => 'null'
                                            ],
                                            [
                                                'text' => '2',
                                                'callback_data' => $prefix . '/2'
                                            ],
                                            [
                                                'text' => '3',
                                                'callback_data' => $prefix . '/3'
                                            ],
                                            [
                                                'text' => '4',
                                                'callback_data' => $prefix . '/4'
                                            ],
                                            [
                                                'text' => '5',
                                                'callback_data' => $prefix . '/5'
                                            ]
                                        ];
                                }
                            } else {
                                $buttons = [
                                        [
                                            'text' => '• 1 •',
                                                'callback_data' => 'null'
                                        ],
                                        [
                                                'text' => '2',
                                                'callback_data' => $prefix . '/2'
                                        ],
                                        [
                                                'text' => '3',
                                                'callback_data' => $prefix . '/3'
                                        ],
                                        [
                                                'text' => '4',
                                                'callback_data' => $prefix . '/4'
                                        ]
                                    ];
                            }
                        } else {
                            $buttons = [
                                    [
                                        'text' => '• 1 •',
                                        'callback_data' => 'null'
                                    ],
                                    [
                                        'text' => '2',
                                        'callback_data' => $prefix . '/2'
                                    ],
                                    [
                                        'text' => '3',
                                        'callback_data' => $prefix . '/3'
                                    ]
                                ];
                        }
                    } elseif ($list == 2) {
                        $buttons = [
                                [
                                    'text' => '• 1 •',
                                    'callback_data' => 'null'
                                ],
                                [
                                    'text' => '2',
                                    'callback_data' => $prefix . '/2'
                                ]
                            ];
                    }
                } else {
                    $buttons = [
                            [
                                'text' => '• 1 •',
                                'callback_data' => 'null'
                            ]
                        ];
                }
            } elseif ($index == 2) {
                if ($list > 3) {
                    if ($list > 4) {
                        if ($list > 5) {
                            $buttons = [
                                    [
                                        'text' => '1',
                                        'callback_data' => $prefix . '/1'
                                    ],
                                    [
                                        'text' => '• 2 •',
                                        'callback_data' => 'null'
                                    ],
                                    [
                                        'text' => '3',
                                        'callback_data' => $prefix . '/3'
                                    ],
                                    [
                                        'text' => '4 ›',
                                        'callback_data' => $prefix . '/4'
                                    ],
                                    [
                                        'text' => "$list ››",
                                        'callback_data' => $prefix . "/$list"
                                    ]
                                ];
                        } else {
                            $buttons = [
                                    [
                                        'text' => '1',
                                        'callback_data' => $prefix . '/1'
                                    ],
                                    [
                                        'text' => '• 2 •',
                                        'callback_data' => 'null'
                                    ],
                                    [
                                        'text' => '3',
                                        'callback_data' => $prefix . '/3'
                                    ],
                                    [
                                        'text' => '4',
                                        'callback_data' => '4'
                                    ],
                                    [
                                        'text' => '5',
                                        'callback_data' => $prefix . '/5'
                                    ]
                                ];
                        }
                    } else {
                        $buttons = [
                                [
                                    'text' => '1',
                                    'callback_data' => $prefix . '/1'
                                ],
                                [
                                    'text' => '• 2 •',
                                    'callback_data' => 'null'
                                ],
                                [
                                    'text' => '3',
                                    'callback_data' => $prefix . '/3'
                                ],
                                [
                                    'text' => '4',
                                    'callback_data' => $prefix . '/4'
                                ]
                            ];
                    }
                } elseif ($list == 3) {
                    $buttons = [
                            [
                                'text' => '1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '• 2 •',
                                'callback_data' => 'null'
                            ],
                            [
                                'text' => '3',
                                'callback_data' => $prefix . '/3'
                            ]
                        ];
                } else {
                    $buttons = [
                            [
                                'text' => '1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '• 2 •',
                                'callback_data' => 'null'
                            ]
                        ];
                }
            } elseif ($index == 3) {
                if ($list > 4) {
                    if ($list > 5) {
                        $buttons = [
                                [
                                    'text' => '1',
                                    'callback_data' => $prefix . '/1'
                                ],
                                [
                                    'text' => '2',
                                    'callback_data' => $prefix . '/2'
                                ],
                                [
                                    'text' => '• 3 •',
                                    'callback_data' => 'null'
                                ],
                                [
                                    'text' => '4 ›',
                                    'callback_data' => $prefix . '/4'
                                ],
                                [
                                    'text' => "$list ››",
                                    'callback_data' => $prefix . "/$list"
                                ]
                            ];
                    } else {
                        $buttons = [
                                [
                                    'text' => '1',
                                    'callback_data' => $prefix . '/1'
                                ],
                                [
                                    'text' => '2',
                                    'callback_data' => $prefix . '/2'
                                ],
                                [
                                    'text' => '• 3 •',
                                    'callback_data' => 'null'
                                ],
                                [
                                    'text' => '4',
                                    'callback_data' => $prefix . '/4'
                                ],
                                [
                                    'text' => '5',
                                    'callback_data' => $prefix . '/5'
                                ]
                            ];
                    }
                } elseif ($list == 4) {
                    $buttons = [
                            [
                                'text' => '1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '2',
                                'callback_data' => $prefix . '/2'
                            ],
                            [
                                'text' => '• 3 •',
                                'callback_data' => 'null'
                            ],
                            [
                                'text' => '4',
                                'callback_data' => $prefix . '/4'
                            ]
                        ];
                } else {
                    $buttons = [
                            [
                                'text' => '1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '2',
                                'callback_data' => $prefix . '/2'
                            ],
                            [
                                'text' => '• 3 •',
                                'callback_data' => 'null'
                            ]
                        ];
                }
            } elseif ($index == 4 && $list <= 5) {
                if ($list == 4) {
                    $buttons = [
                            [
                                'text' => '1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '2',
                                'callback_data' => $prefix . '/2'
                            ],
                            [
                                'text' => '3',
                                'callback_data' => $prefix . '/3'
                            ],
                            [
                                'text' => '• 4 •',
                                'callback_data' => 'null'
                            ]
                        ];
                } elseif ($list == 5) {
                    $buttons = [
                            [
                                'text' => '1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '2',
                                'callback_data' => $prefix . '/2'
                            ],
                            [
                                'text' => '3',
                                'callback_data' => $prefix . '/3'
                            ],
                            [
                                'text' => '• 4 •',
                                'callback_data' => 'null'
                            ],
                            [
                                'text' => '5',
                                'callback_data' => $prefix . '/5'
                            ]
                        ];
                }
            } elseif ($index == 5 && $list == 5) {
                $buttons = [
                        [
                            'text' => '1',
                            'callback_data' => $prefix . '/1'
                        ],
                        [
                            'text' => '2',
                            'callback_data' => $prefix . '/2'
                        ],
                        [
                            'text' => '3',
                            'callback_data' => $prefix . '/3'
                        ],
                        [
                            'text' => '4',
                            'callback_data' => $prefix . '/4'
                        ],
                        [
                            'text' => '• 5 •',
                            'callback_data' => 'null'
                        ]
                    ];
            } else {
                if ($index < $list - 2) {
                    $indexm = $index - 1;
                    $indexp = $index + 1;
                    $buttons = [
                            [
                                'text' => '‹‹ 1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '‹ ' . $indexm,
                                'callback_data' => $prefix . '/' . $indexm
                            ],
                            [
                                'text' => '• ' . $index . ' •',
                                'callback_data' => 'null',
                            ],
                            [
                                'text' => $indexp . ' ›',
                                'callback_data' => $prefix . '/' . $indexp
                            ],
                            [
                                'text' => $list . ' ››',
                                'callback_data' => $prefix . '/' . $list
                            ]
                        ];
                } elseif ($index == ($list - 2)) {
                    $indexm = $index - 1;
                    $indexp = $index + 1;
                    $buttons = [
                            [
                                'text' => '‹‹1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '' . $indexm,
                                'callback_data' => $prefix . '/' . $indexm
                            ],
                            [
                                'text' => '• ' . $index . ' •',
                                'callback_data' => 'null',
                            ],
                            [
                                'text' => '' . $indexp,
                                'callback_data' => $prefix . '/' . $indexp
                            ],
                            [
                                'text' => "$list",
                                'callback_data' => $prefix . "/$list"
                            ]
                        ];
                } elseif ($index == ($list - 1)) {
                    $indexm = $index - 1;
                    $indexmm = $index - 2;
                    $buttons = [
                            [
                                'text' => '‹‹ 1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '‹ ' . $indexmm,
                                'callback_data' => $prefix . '/' . $indexmm
                            ],
                            [
                                'text' => '' . $indexm,
                                'callback_data' => $prefix . '/' . $indexm
                            ],
                            [
                                'text' => '• ' . $index . ' •',
                                'callback_data' => $prefix . '/' . $index
                            ],
                            [
                                'text' => "$list",
                                'callback_data' => $prefix . "/$list"
                            ]
                        ];
                } elseif ($index == $list) {
                    $indexm = $index - 1;
                    $indexmm = $index - 2;
                    $indexmmm = $index - 3;
                    $buttons = [
                            [
                                'text' => '‹‹ 1',
                                'callback_data' => $prefix . '/1'
                            ],
                            [
                                'text' => '‹ ' . $indexmmm,
                                'callback_data' => $prefix . '/' . $indexmmm
                            ],
                            [
                                'text' => '' . $indexmm,
                                'callback_data' => $prefix . '/' . $indexmm,
                            ],
                            [
                                'text' => '' . $indexm,
                                'callback_data' => $prefix . '/' . $indexm
                            ],
                            [
                                'text' => '• ' . $index . ' •',
                                'callback_data' => $prefix . '/' . $index
                            ]
                        ];
                }
            }
        }

        // If there are other buttons in this row (checking the column)
        if ($this->column !== 0) {
            // Go to the next
            $this->changeRow();
        }

        $this->inline_keyboard[$this->row] = $buttons;

        // We added a row
        $this->changeRow();
    }

    /** @} */

    /** @} */
}
