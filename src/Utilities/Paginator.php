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

namespace PhpBotFramework\Utilities;

/**
 * \addtogroup Modules
 * @{
 */

// Delimitate items in one page.
define("DELIMITER", "::::::::::::::::::::::::::::::::::::::\n");

/* \class Paginator */
class Paginator
{
    /**
     * \brief Given the items, organize them in pages.
     * \details Allows the bot to show various pages where each has the same amount of items.
     * @param array $items The items to show.
     * @param int $index The page's index.
     * @param InlineKeyboard $keyboard The inline keyboard.
     * @param function $format_item The callback function which format a single item.
     * @param int $item_per_page The amount of items to show in one page.
     * @param string $delimiter The delimiter for items.
     * @return Message $message The message to send.
     */
    public static function paginateItems($items, int $index, &$keyboard, $format_item, int $item_per_page = 3,
                                         $prefix = 'list', string $delimiter = DELIMITER)
    {
        // Assign the position of first item to show
        $item_position = ($index - 1) * $item_per_page + 1;

        $items_number = $items->rowCount();

        $counter = 1;

        $items_displayed = 0;

        $total_pages = intval($items_number / $item_per_page);

        // If there an incomplete page
        if (($items_number % $item_per_page) != 0) {
            $total_pages++;
        }

        // Initialize keyboard with the list
        $keyboard->addListKeyboard($index, $total_pages, $prefix);

        $message = '';

        // Iterate over all results
        while ($item = $items->fetch()) {
            // If we have to display the first item of the page and we
            // found the item to show (using the position calculated before)
            if ($items_displayed === 0 && $counter === $item_position) {
                $message .= $format_item($item, $keyboard);
                $items_displayed++;
            // If there are space for other items
            } elseif ($items_displayed > 0 && $items_displayed < $item_per_page) {
                $message .= $delimiter;
                $message .= $format_item($item, $keyboard);

                $items_displayed++;
            } elseif ($items_displayed === $item_per_page) {
                break;
            } else {
                $counter++;
            }
        }

        return $message;
    }
}

/*
 * @}
 */
