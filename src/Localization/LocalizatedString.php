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

/**
 * \addtogroup Modules
 * @{
 */

/** \class LocalizatedString
 */
trait LocalizatedString
{

    /** @} */

    /**
     * \addtogroup Localization Localization
     * @{
     */

    public function getStr($index)
    {

        if (!isset($this->language)) {
            throw BotException("Language not set");
        }

        // If the language of the user is already set in the array containing localizated strings
        if (!isset($this->local[$this->language])) {
            // Is the bot using webhook?
            if (isset($this->webhook)) {
                // Load only the user language in array
                $this->loadSingleLanguage($this->localization_dir);
            } else {
                // Load all localization files
                $this->loadLocalization($this->localization_dir);
            }
        }



        // Check if the string needed exists
        if (isset($this->local[$this->language][$index])) {
            // If yes, return it
            return $this->local[$this->language][$index];
        }

        // Throw an error
        throw BotException("$index is not set for {$this->language}");
    }

    /** @} */
}
