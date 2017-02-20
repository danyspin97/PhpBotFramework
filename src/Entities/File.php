<?php

namespace PhpBotFramework\Entities;

use PhpBotFramework\Exceptions\BotException;

class File
{
    private $file;

    private $format_name;

    /**
     * \brief Fill this object with another file.
     * @param string $file File_id or local/remote path to the file.
     * @param string $format_name Format name of the file (audio, document, ...)
     */
    public function init(string $file, string $format_name)
    {
        $this->file = $file;
        $this->format_name = $format_name;
    }

    /**
     * \brief (<i>Internal</i>) Check if the path to the file given is local or a file_id/url.
     * @return bool True if the file is a local path.
     */
    public function is_local() : bool
    {
        $host = parse_url($this->file, PHP_URL_HOST);

        // If it has not an url host and it is a file_id
        if ($host === null && !is_numeric($this->file)) {
            // Then it is a local path
            return true;
        } else {
            return false;
        }
    }

    /**
     * \brief (<i>Internal</i>) Get string component of this file.
     * @return string File as string (file_id/path to the file).
     */
    public function getString()
    {
        return $this->file;
    }

    public function getResource()
    {
        $resource = fopen($this->file, 'r');

        if ($resource !== false) {
            return $resource;
        }

        return false;
    }

    public function getFormatName() : string {
        return $this->format_name;
    }
}
