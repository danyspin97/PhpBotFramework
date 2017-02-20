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

trait File
{

    /**
     * \addtogroup Localization Localization
     * \brief Methods to create a localized bot.
     * \details Localization files must have this syntax:
     * file <code>./localization/en.json</code>:
     *
     *     {"Hello_Msg": "Hello"}
     *
     * File <code>./localization/it.json</code>:
     *
     *     {"Hello_Msg": "Ciao"}
     *
     * Usage in <code>processMessage()</code>:
     *
     *     $sendMessage($this->local[$this->language]["Hello_Msg"]);
     *
     * @{
     */

    /** \brief (<i>Internal</i>)Store the localizated strings. */
    protected $local;

    /** \brief Directory where there are the localization files. */
    protected $localization_dir = './localization';

    /**
     * \brief (<i>Internal</i>)Load a localization file into the localized strings array.
     * @param string $lang Language to load.
     * @param string $dir Directory in which there are the JSON files.
     * @return bool True if loaded.
     */
    protected function loadSingleLanguage(string $lang = 'en', string $dir = './localization') : bool
    {
        // Name of the file
        $filename = "$dir/$lang";

        // If this language isn't already set
        if (!isset($this->local[$lang])) {
            // and the file exists
            if (file_exists($filename)) {
                // Load localization in memory
                $this->local[$lang] = json_decode(file_get_contents($filename), true);

                // We loaded it
                return true;
            }
            // The file doens't exists
            return false;
        }

        // The language is already set
        return true;
    }

    /**
     * \brief Load all localization files (JSON-serialized) from a folder and set them in $local variable.
     * \details Save all localization files, saved as json format, from a directory and put the contents in $local variable.
     * Each file will be saved into $local with the first two letters of the filename as the index.
     * @param string $dir Directory where the localization files are saved.
     * @return bool True if the directory could be opened without errors.
     */
    public function loadAllLocalizations(string $dir = './localization') : bool
    {
        // Open directory
        if ($handle = opendir($dir)) {
            // Iterate over all files
            while (false !== ($file = readdir($handle))) {
                // If the file is a JSON data file
                if (strlen($file) > 6 && substr($file, -5) === '.json') {
                    try {
                        // Add the contents of the file to the $local variable, after deserializng it from JSON format
                        // The contents will be added with the 2 letter of the file as the index
                        $this->local[substr($file, 0, 2)] = json_decode(file_get_contents("$dir/$file"), true);
                    } catch (BotException $e) {
                        echo $e->getMessage();
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * \brief Load localization for current language and mode.
     * \details This method will load only the current user/group localization file if the bot is using webhook, all files otherwise.
     * @param string $dir Directory where the localization file is stored. If no directory is given (by default) it will load it from $localization_dir (which is ./localization if it is not set).
     * @return bool True if the localization has been loaded or it is already loaded.
     */
    public function loadCurrentLocalization(string $dir = '') : bool
    {
        // If no dir has been given
        if (!isset($dir) || $dir === '') {
            // The dir is the default one
            $dir = $this->localization_dir;
        }

        // If the language of the user is already set in the array containing localizated strings
        if (!isset($this->local[$this->language])) {
            // Is the bot using webhook?
            if (isset($this->_is_webhook)) {
                return $this->loadSingleLanguage($this->localization_dir);
            } else {
                return $this->loadAllLocalizations($this->localization_dir);
            }
        }

        return false;
    }

    /**
     * \brief Set location of localization files.
     * @param string $dir Directory where the files are stored.
     */
    public function setLocalizationDir(string $dir)
    {
        $this->localization_dir = $dir;
    }

    /** @} */
}
