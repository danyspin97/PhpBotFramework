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

namespace PhpBotFramework\Database;

use PhpBotFramework\Exceptions\BotException;

/**
 * \class Getter Database Getter
 * \brief Get PDO and redis object from bot, if any.
 */
trait Getter
{
    /** @internal
     * \brief Database handler object. */
    public $database;

    /** @internal
     * \brief Redis object. */
    public $redis;

    /**
     * \brief Get PDO object.
     * @return PDO Connection object.
     */
    public function getPdo() : \PDO
    {
        if (!isset($this->database)) {
            throw new BotException("Database connection has not been set");
        }

        return $this->database->pdo;
    }

    /**
     * \brief Get Redis object.
     * @return Redis Conneciton object.
     */
    public function getRedis() : \Redis
    {
        if (!isset($this->redis)) {
            throw new BotException("Redis connection has not been set");
        }

        return $this->redis;
    }
}
