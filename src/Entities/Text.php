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

/**
 * \addtogroup Utility-classes Utility classes
 * \brief Static methods grouped by class and context.
 * @{
 */

/**
 * \class Text
 * \brief Contains text helper methods
 */
class Text
{
    /**
     * \brief Get hashtag contained in a string.
     * \details Check hashtags in a string using a regular expression.
     * All valid hashtags will be returned in an array.
     * See the following [StackOverflow thread](http://stackoverflow.com/questions/3060601/retrieve-all-hashtags-from-a-tweet-in-a-php-function) to learn more.
     * @param $string The string to check for hashtags.
     * @return array An array of valid hashtags, can be empty.
     */
    public static function getHashtags(string $string) : array
    {
        if (preg_match_all("/(#\w+)/u", $string, $matches) != 0) {
            $hashtagsArray = array_count_values($matches[0]);
            $hashtags = array_keys($hashtagsArray);
        }

        return $hashtags ?? [];
    }

    /**
     * \brief Convert a string into camelCase style.
     * \details Take a look [here](http://www.mendoweb.be/blog/php-convert-string-to-camelcase-string/)
     * to learn more.
     * @param string $str The string to convert.
     * @param array $noStrip
     * @return string $str The input string converted to camelCase.
     */
    public static function camelCase($str, array $noStrip = [])
    {
        // Non-alpha and non-numeric characters become spaces.
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);

        // Make each word's letter uppercase.
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

    /**
     * \brief Remove HTML formattation from Telegram usernames.
     * \details Remove the $modificator html formattation from a message
     * containing Telegram usernames.
     * @param string $string to parse.
     * @param string $tag Formattation tag to remove.
     * @return string The string, modified if there are usernames. Otherwise $string.
     */
    public static function removeUsernameFormattation(string $string, string $tag) : string
    {
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

        return $string;
    }

    /** @} */
}
