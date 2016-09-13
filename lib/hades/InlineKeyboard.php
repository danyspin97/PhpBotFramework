<?php

namespace WiseDragonStd\HadesWrapper;

class InlineKeyboard {
    protected $inline_keyboard;
    protected $bot;

    public function __construct($bot = null, $array = null) {
        $this->bot = &$bot;
        $this->inline_keyboard = $array ?? array();
    }

    public function &getKeyboard() {
        $reply_markup = ['inline_keyboard' => $this->inline_keyboard];
        $reply_markup = json_encode($reply_markup);
        $this->clearKeyboard();
        return $reply_markup;
    }

    public function &getNoJSONKeyboard() {
        $reply_markup = ['inline_keyboard' => $this->inline_keyboard];
        $this->clearKeyboard();
        return $reply_markup;
    }

    public function addLevelButtons(...$buttons) {
        $this->inline_keyboard[] = $buttons;
    }

    public function clearKeyboard() {
        $this->inline_keyboard = [];
    }

    public function &getBackKeyboard() {
        $inline_keyboard = [ 'inline_keyboard' =>
            [
                [
                    [
                        'text' => &$this->bot->localization[$this->bot->language]['Back_Button'],
                        'callback_data' => 'back'
                    ]
                ]
            ]
        ];
        return json_encode($inline_keyboard);
    }

    public function &getBackSkipKeyboard() {
        $inline_keyboard = [ 'inline_keyboard' =>
            [
                [
                    [
                        'text' => &$this->bot->localization[$this->bot->language]['Back_Button'],
                        'callback_data' => 'back'
                    ],
                    [
                        'text' => &$this->bot->localization[$this->bot->language]['Skip_Button'],
                        'callback_data' => 'skip'
                    ]
                ]
            ]
        ];
        return json_encode($inline_keyboard);
    }

    public function &getChooseLanguageKeyboard() {
        $inline_keyboard = ['inline_keyboard' => array()];
        foreach($this->bot->localization['launguages'] as $languages => $language_msg) {
            if (strpos($languages, $this->bot->language) !== false) {
                array_push($inline_keyboard['inline_keyboard'], [
                    [
                        'text' => $language_msg,
                        'callback_data' => 'same/language'
                    ]
                ]);
            } else {
                array_push($inline_keyboard['inline_keyboard'], [
                    [
                        'text' => $language_msg . '/' . $this->bot->localization[$this->bot->language][$languages],
                        'callback_data' => 'cl/' . $languages
                    ]
                ]);
            }
        }
        unset($languages);
        unset($language_msg);
        array_push($inline_keyboard['inline_keyboard'], [
            [
                'text' => &$this->bot->localization[$this->bot->language]['Back_Button'],
                'callback_data' => 'back'
            ]
        ]);
        return json_encode($inline_keyboard);
    }

    public function &getListKeyboard($index, $list, $menu_button = false, $search_button = false, $search_mode = false, $extra_buttons0 = array(), $extra_buttons1 = array(), $extra_buttons2 = array()) {
        if (($list > 0) && ($index >= 0)) {
            if (!$search_mode) {
                $prefix = 'list';
            } else {
                $prefix = 'search';
            }
            if ($index == 0) {
                if ($list > 1) {
                    if ($list > 2) {
                        if ($list > 3) {
                            if ($list > 4) {
                                if ($list > 5) {
                                    $inline_keyboard = [ 'inline_keyboard' =>
                                        [
                                            [
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
                                                ]
                                            ]
                                    ];
                                } else {
                                    $inline_keyboard = [ 'inline_keyboard' =>
                                        [
                                            [
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
                                            ]
                                        ]
                                    ];
                                }
                            } else {
                                $inline_keyboard = [ 'inline_keyboard' =>
                                    [
                                        [
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
                                        ]
                                    ]
                                ];
                            }
                        } else {
                            $inline_keyboard = [ 'inline_keyboard' =>
                                [
                                    [
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
                                    ]
                                ]
                            ];
                        }
                    } elseif ($list == 2) {
                        $inline_keyboard = [ 'inline_keyboard' =>
                            [
                                [
                                    [
                                        'text' => '1',
                                        'callback_data' => $prefix . '/1'
                                    ],
                                    [
                                        'text' => '2',
                                        'callback_data' => $prefix . '/2'
                                    ],
                                ]
                            ]
                        ];
                    }
                } else {
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
                                [
                                    'text' => '1',
                                    'callback_data' => $prefix . '/1'
                                ]
                            ]
                        ]
                    ];
                }
            } else if ($index == 1) {
                if ($list > 1) {
                    if ($list > 2) {
                        if ($list > 3) {
                            if ($list > 4) {
                                if ($list > 5) {
                                    $inline_keyboard = [ 'inline_keyboard' =>
                                        [
                                            [
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
                                                ]
                                            ]
                                    ];
                                } else {
                                    $inline_keyboard = [ 'inline_keyboard' =>
                                        [
                                            [
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
                                            ]
                                        ]
                                    ];
                                }
                            } else {
                                $inline_keyboard = [ 'inline_keyboard' =>
                                    [
                                        [
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
                                        ]
                                    ]
                                ];
                            }
                        } else {
                            $inline_keyboard = [ 'inline_keyboard' =>
                                [
                                    [
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
                                    ]
                                ]
                            ];
                        }
                    } elseif ($list == 2) {
                        $inline_keyboard = [ 'inline_keyboard' =>
                            [
                                [
                                    [
                                        'text' => '• 1 •',
                                        'callback_data' => 'null'
                                    ],
                                    [
                                        'text' => '2',
                                        'callback_data' => $prefix . '/2'
                                    ],
                                ]
                            ]
                        ];
                    }
                } else {
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
                                [
                                    'text' => '• 1 •',
                                    'callback_data' => 'null'
                                ]
                            ]
                        ]
                    ];
                }
            } elseif ($index == 2) {
                if ($list > 3) {
                    if ($list > 4) {
                        if ($list > 5) {
                            $inline_keyboard = [ 'inline_keyboard' =>
                                [
                                    [
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
                                    ]
                                ]
                            ];
                        } else {
                            $inline_keyboard = [ 'inline_keyboard' =>
                                [
                                    [
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
                                    ]
                                ]
                            ];
                        }
                    } else {
                        $inline_keyboard = [ 'inline_keyboard' =>
                            [
                                [
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
                                    ],
                                ]
                            ]
                        ];
                    }
                } elseif ($list == 3) {
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                            ]
                        ]
                    ];
                } else {
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
                                [
                                    'text' => '1',
                                    'callback_data' => $prefix . '/1'
                                ],
                                [
                                    'text' => '• 2 •',
                                    'callback_data' => 'null'
                                ]
                            ]
                        ]
                    ];
                }
            } elseif ($index == 3) {
                if ($list > 4) {
                    if ($list > 5) {
                        $inline_keyboard = [ 'inline_keyboard' =>
                            [
                                [
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
                                    ],
                                ]
                            ]
                        ];
                    } else {
                        $inline_keyboard = [ 'inline_keyboard' =>
                            [
                                [
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
                                ]
                            ]
                        ];
                    }
                } elseif ($list == 4) {
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                            ]
                        ]
                    ];
                } else {
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                            ]
                        ]
                    ];
                }
            } elseif ($index == 4 && $list <= 5) {
                if ($list == 4) {
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                            ]
                        ]
                    ];
                } else if ($list == 5) {
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                                ],
                            ]
                        ]
                    ];
                }
            } else if ($index == 5 && $list == 5) {
                $inline_keyboard = [ 'inline_keyboard' =>
                    [
                        [
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
                            ],
                        ]
                    ]
                ];
            } else {
                if ($index < $list - 2) {
                    $indexm = $index - 1;
                    $indexp = $index + 1;
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                            ]
                        ]
                    ];
                } elseif ($index == ($list - 2)) {
                    $indexm = $index - 1;
                    $indexp = $index + 1;
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                            ]
                        ]
                    ];
                } elseif ($index == ($list - 1)) {
                    $indexm = $index - 1;
                    $indexmm = $index - 2;
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                            ]
                        ]
                    ];
                } else if ($index == $list) {
                    $indexm = $index - 1;
                    $indexmm = $index - 2;
                    $indexmmm = $index - 3;
                    $inline_keyboard = [ 'inline_keyboard' =>
                        [
                            [
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
                            ]
                        ]
                    ];
                }
            }
        }
        if ($extra_buttons0 != null && empty($extra_buttons0) == false) {
            array_push($inline_keyboard["inline_keyboard"], [$extra_buttons0]);
        }
        if($search_button) {
            array_push($inline_keyboard['inline_keyboard'], [
                [
                    'text' => $this->bot->localization[$this->bot->language]['Search_Button'],
                    'callback_data' => 'search'
                ]
            ]);
        } elseif ($search_mode) {
            array_push($inline_keyboard['inline_keyboard'], [
                [
                    'text' => $this->bot->localization[$this->bot->language]['Show_Button'],
                    'callback_data' => 'back'
                ],
                [
                    'text' => $this->bot->localization[$this->bot->language]['NewSearch_Button'],
                    'callback_data' => 'search'
                ]
            ]);
        }
        if ($menu_button) {
            array_push($inline_keyboard['inline_keyboard'], [
                [
                    'text' => $this->bot->localization[$this->bot->language]['Menu_Button'],
                    'callback_data' => 'menu'
                ]
            ]);
        }
        if ($extra_buttons1 != null && empty($extra_buttons1) == false) {
            array_push($inline_keyboard["inline_keyboard"], [$extra_buttons1]);
        }

        if ($extra_buttons2 != null && empty($extra_buttons2) == false) {
            array_push($inline_keyboard["inline_keyboard"], [$extra_buttons2]);
        }
        return json_encode($inline_keyboard);
    }

    public function &getCompositeListKeyboard($index, $list, $prefix) {
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
            } else if ($index == 1) {
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
                } else if ($list == 5) {
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
            } else if ($index == 5 && $list == 5) {
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
                } else if ($index == $list) {
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
        $this->inline_keyboard[] = $buttons;
    }
}
