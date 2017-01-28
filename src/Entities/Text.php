<?php

namespace PhpBotFramework\Entities;

/**
 * \addtogroup Utility-classes Utility class
 * \brief Static methods grouped by class and context.
 * @{
 */

/**
 * \class Text
 * \brief Contains text helper methods
 */
class Text {

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

    // http://www.mendoweb.be/blog/php-convert-string-to-camelcase-string/<Paste>
    public static function camelCase($str, array $noStrip = [])
{
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
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

    /** @} */

}
