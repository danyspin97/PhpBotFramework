<?php

namespace DanySpin97\PhpBotFramework;

define("DELIMITER", '::::::::::::::::::::::::::::::::::::::
    ');

/**
 * \class Utility
 * \brief Contains static help methods.
 */
class Utility {

    /**
     * \addtogroup Utility-methods Utility methods
     * \brief Helper methods.
     * @{
     */

    /**
     * \brief Get hashtag contained in a string.
     * \details Check hashtags in a string using regex.
     * All valid hashtags will be returned in an array.
     * [Credis to trante](http://stackoverflow.com/questions/3060601/retrieve-all-hashtags-from-a-tweet-in-a-php-function).
     * @param $string The string to check for hashtags.
     * @return An array of valid hashtags, can be empty.
     */
    public static function getHashtags(string $string) : array {

        // Use regex to check
        if (preg_match_all("/(#\w+)/u", $string, $matches) != 0) {

            $hashtagsArray = array_count_values($matches[0]);

            $hashtags = array_keys($hashtagsArray);

        }

        // Return an array of hashtags
        return $hashtags ?? [];
    }

    /**
     * \brief Remove html formattation from telegram usernames in string.
     * \details Remove the $modificator html formattation from a message containing telegram username, to let the user click them.
     * @param $string to parse.
     * @param $tag Formattation tag to remove.
     * @return The string, modified if there are usernames. Otherwise $string.
     */
    public static function removeUsernameFormattation(string $string, string $tag) : string {

        // Check if there are usernames in string using regex
        if (preg_match_all('/(@\w+)/u', $string, $matches) != 0) {

            $usernamesArray = array_count_values($matches[0]);

            $usernames = array_keys($usernamesArray);

            // Count how many username we've got
            $count = count($usernames);

            // Delimitator to make the formattation start
            $delimitator_start = '<' . $tag . '>';

            // and to make it end
            $delimitator_end = '</' . $tag . '>';

            // For each username
            for ($i = 0; $i !== $count; $i++) {

                // Put the Delimitator_end before the username and the start one after it
                $string = str_replace($usernames[$i], $delimitator_end . $usernames[$i] . $delimitator_start, $string);

            }

        }

        // Return the string, modified or not
        return $string;

    }

    static public function paginateItems($items, int $index, &$keyboard, $format_item, int $item_per_page = 3, $prefix = 'list', string $delimiter = DELIMITER) {

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

