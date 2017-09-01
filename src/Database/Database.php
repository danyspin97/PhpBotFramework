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

use PhpBotFramework\Core\BasicBot;
use PhpBotFramework\Exceptions\BotException;

define('PDO_DEFAULT_ADAPTER', 'mysql');

/** \class Database Database connection handler
 */
class Database
{
    use User;

    /** @} */

    private $bot;

    /** PDO connection to the database. */
    public $pdo;

    /** \brief Table contaning bot users data in the SQL database. */
    public $user_table = 'TelegramUser';

    /**
     * \addtogroup Database
     * @{
     */

    /**
     * @internal
     * \brief Create a Database handler object.
     * @param BasicBot $bot Reference to the bot that use this object.
     */
    public function __construct(BasicBot &$bot)
    {
        $this->bot = $bot;
    }

    /**
     * \brief Open a database connection using PDO.
     * \details Provides a simple way to initialize a database connection
     * and create a PDO instance.
     * @param array $params Parameters for initialize connection.
     * Index required:
     *     - <code>username</code>
     *     - <code>password</code> (can be an empty string)
     * Optional index:
     *     - <code>dbname</code>
     *     - <code>adapter</code> <b>Default</b>: <code>mysql</code>
     *     - <code>host</code> <b>Default</b>: <code>localhost</code>
     *     - <code>options</code> (<i>Array of options passed when creating PDO object</i>)
     * @return bool True when the connection is succefully created.
     */
    public function connect(array $params) : bool
    {
        $params = $this->addDefaultValue($params);
        $config = $this->getDns($params);

        try {
            $this->pdo = new \PDO($config, $params['username'], $params['password'], $params['options']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return true;
        } catch (\PDOException $e) {
            echo 'Unable to connect to database, an error occured:' . $e->getMessage();
        }

        return false;
    }

    /**
     * @internal
     * \brief Add default connection value to parameter passed to PDO.
     * @param array $params Parameter for PDO connection.
     * @return array Parameter with defaults value.
     */
    public static function addDefaultValue(array $params) : array
    {
        static $defaults = [ 'adapter' => PDO_DEFAULT_ADAPTER, 'host' => 'localhost', 'options' => [] ];
        return array_merge($defaults, $params);
    }

    /**
     * @internal
     * \brief Returns a string that can passed to PDO as DNS parameter in order to open connection.
     * @param array $params Array containing parameter of the connection
     * @return string Parameters contained in array $params sanitized in a string that can be passed as DNS param of PDO object creation.
     */
    public static function getDns($params) : string
    {
        $response = $params['adapter'] . ':';
        unset($params['adapter']);
        $fields = [];

        foreach ($params as $field => $value) {
            /**
             *Check if the current field matches one of the fields
             * that are passed to PDO in another way and so don't need
             * to be included in the string.
             */
            if ($field === 'username' || $field === 'password' || $field === 'options') {
                unset($params[$field]);
                continue;
            }

            $fields[] = $field . '=' . $value;
        }

        return $response . join(';', $fields);
    }

    /** @} */
}
