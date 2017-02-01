<?php

namespace PhpBotFramework\Database;

use PDOException;

trait Handler {

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
    public function connect($params) {
        try {
            $config = $this->stringify($this->mergeWithDefaults($params));

            $this->pdo = new \PDO($config, $params['username'], $params['password']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return true;
        } catch (PDOException $e) {
            echo 'Unable to connect to database, an error occured:' . $e->getMessage();
        }

        return false;
    }

    protected function mergeWithDefaults($params) {
        $DEFAULTS = [ 'adapter' => 'mysql', 'host' => 'localhost' ];
        return array_merge($DEFAULTS, $params);
    }

    /** \brief Returns a string that can passed to PDO in order to open connection. */
    protected function stringify($params) : string {
        $response = $params['adapter'] . ':';
        $fields = [];

        foreach ($params as $field => $value) {
            /**
             *Check if the current field matches one of the fields
             * that are passed to PDO in another way and so don't need
             * to be included in the string.
             */
            if (in_array($field, ['adapter', 'username', 'password'])) {
                unset($params[$field]);
                continue;
            }

            array_push($fields, $field . '=' . $value);
        }

        return $response . join(';', $fields);
    }

    /** @} */

}
