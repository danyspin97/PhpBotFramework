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

define("DELIMITER", "::::::::::::::::::::::::::::::::::::::\n");

class Paginator
{
    public static function paginateItems($items, int $index, &$keyboard, $format_item, int $item_per_page = 3, $prefix = 'list', string $delimiter = DELIMITER)
    {

        // Calc the position of the first item to show
        $item_position = ($index - 1) * $item_per_page + 1;

        // Counter variable
        $cont = 1;

        // How many items did we display?
        $items_displayed = 0;

        // Get how many items did the database return
        $items_number = $items->rowCount();

        // Get how many complete pages there are
        $total_pages = intval($items_number / $item_per_page);

        // If there an incomplete page
        if (($items_number % $item_per_page) != 0) {
            $total_pages++;
        }

        // Initialize keyboard with the list
        $keyboard->addListKeyboard($index, $total_pages, $prefix);

        // Initialize empty string
        $message = '';

        // Iterate over all results
        while ($item = $items->fetch()) {
            // If we have to display the first item of the page and we found the item to show (using the position
            // calculated before)
            if ($items_displayed === 0 && $cont === $item_position) {
                // Format the item using closure
                $message .= $format_item($item, $keyboard);

                // We displayed an item
                $items_displayed++;

                // If we displayed at least an item but still not how much we want
            } elseif ($items_displayed > 0 && $items_displayed < $item_per_page) {
                // Add delimiter to the message
                $message .= $delimiter;

                // Format the item using closure
                $message .= $format_item($item, $keyboard);

                // We displayed an item
                $items_displayed++;

                // If we displayed all the item we wanted
            } elseif ($items_displayed === $item_per_page) {
                // Exit the cycle
                break;

                // We are just iterating over an unwanted result
            } else {
                $cont++;
            }
        }

        // Return the created string
        return $message;
    }
}
