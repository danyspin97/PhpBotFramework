<?php

namespace PhpBotFramework\Database;

use PhpBotFramework\Exceptions\BotException;

trait Getter
{
    public $database;

    public $redis;

    public function getPDO() : \PDO
    {
        if (!isset($this->database))
        {
            throw new BotException("Database connection has not been set");
        }

        return $this->database->pdo;
    }

    public function getRedis() : \Redis
    {
        if (!isset($this->redis))
        {
            throw new BotException("Redis connection has not been set");
        }

        return $this->redis;
    }
}
