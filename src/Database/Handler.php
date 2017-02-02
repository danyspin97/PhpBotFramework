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

use PDOException;

define('DATA_CONNECTION_FILE', './data/connection.json');

trait Handler
{

    /**
     * \addtogroup Bot Bot
     * @{
     */

    /** Pdo connection to the database. */
    public $pdo;

    /**
     * \brief Open a database connection using PDO.
     * \details Provides a simpler way to initialize a database connection
     * and create a PDO instance.
     * @param $params Parameters for initialize connection.
     * @return True on success.
     */
    public function connect(array $params = []) : bool
    {
        if (empty($params) && file_exists('DATA_CONNECTION_FILE')) {
            $params = json_decode(file_get_contents('DATA_CONNECTION_FILE'), true);
        }

        try {
            $config = $this->stringify($this->mergeWithDefaults($params));

            $this->pdo = new \PDO($config, $params['username'], $params['password'], $params['option']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return true;
        } catch (PDOException $e) {
            echo 'Unable to connect to database, an error occured:' . $e->getMessage();
        }

        return false;
    }

    protected function mergeWithDefaults($params)
    {
        $DEFAULTS = [ 'adapter' => 'mysql', 'host' => 'localhost' ];
        return array_merge($DEFAULTS, $params);
    }

    /** \brief Returns a string that can passed to PDO in order to open connection.
     * @param array $params Array containing parameter of the connection
     */
    protected function stringify($params) : string
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
            if ($field === 'username' || $field === 'password') {
                unset($params[$field]);
                continue;
            }

            $fields[] = $field . '=' . $value;
        }

        return $response . join(';', $fields);
    }

    /** @} */
}
