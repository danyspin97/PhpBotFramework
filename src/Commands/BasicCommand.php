<?php

namespace PhpBotFramework\Commands;

/**
 * Class Basic command
 */
class BasicCommand
{
    protected $script;

    public function getScript() : callable
    {
        return $this->script;
    }
}
