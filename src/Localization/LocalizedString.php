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

use PhpBotFramework\Exceptions\BotException;

/**
 * \class LocalizedString
 * \brief Get a localized string using user language and localization files.
 */
trait LocalizedString
{
    /** @} */

    abstract public function getLanguageRedis(string $default_language = 'en', int $expiring_time = 86400) : string;

    abstract public function loadCurrentLocalization(string $dir = '') : bool;

    /**
     * \addtogroup Localization Localization
     * @{
     */

    /** @internal
      * \brief Reference to the bot. */
    protected $bot;

    /** @internal
      * \brief Current user/group language. */
    public $language;

    /** @internal
      * \brief Store the localizated strings. */
    protected $local;

    /**
     * \brief Get a localized string giving an index.
     * \details Using LocalizedString::language this method get the string of the index given localized string in language of the current user/group.
     * This method will load the language first, using PhpBotFramework\Localization\Language::getLanguage(), if the language has not been set.
     * If the localization file for the user/group language has not been load yet, it will load it (load only the single localization file if the bot is using webhook, load all otherwise).
     * @param string $index Index of the localized string to get.
     * @return string Localized string in the current user/group language.
     */
    public function getStr($index)
    {
        if (!isset($this->language)) {
            $this->getLanguageRedis();
        }

        $this->loadCurrentLocalization();

        // Check if the requested string exists
        if (isset($this->local[$this->language][$index])) {
            return $this->local[$this->language][$index];
        }

        throw new BotException("Index '$index' is not set for {$this->language}");
    }

    /** @} */
}
