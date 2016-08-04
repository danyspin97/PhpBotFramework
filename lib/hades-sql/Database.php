<?php

namespace WiseDragonStd\HadesWrapper;

class Database {
    protected $pdo;
    protected $bot;
    public $table = null;
    public $where_condition = null;

    public function __construct($driver, $dbname, $user, $password) {
        $this->pdo = new PDO("$driver:host=localhost;dbname=$dbname", $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    // Get pdo object
    public function &getPDO() {
        return $this->pdo;
    }

    /**
     * This function takes as arguments a valid SQL query and an
     * optional callback function to call on the returned records.
     * The callback must accept an argument who is the single record's row.
     * @example
     *   $crud->execute("select * from users", function($row){ print_r($row); });
     */
    public function execute($query, $callback = null) {
        $statement = $this->pdo->query($query);

        /** Users can avoid to specify a callback function */
        if ($callback != null) {
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $callback($row);
            }
        }

        return 0;
    }

    /**
     * == SYNTAX SUGAR
     * HadesSQL provides some 'scope function' that change the
     * table's scope for the current database object.
     * @example
     *   $db->from("user")->delete(["*"], "where" => "id = 1")
     */

    public function from($table) {
        $this->table = $table;
        return $this;
    }

    public function into($table) {
        $this->table = $table;
        return $this;
    }

    public function where($condition) {
        $this->where_condition = $condition;
        return $this;
    }


    /**
     * == CREATE
     * This method takes as first argument the table's name and
     * as second argument an hash table which contains all table's columns
     * @example
     *   $crud->createTable("user", ["name" => "text", "money" => "real"])
     */
    public function createTable($name, $columns) {
        /* Raise an exception if no columns are defined */
        if (empty($columns) == 1) {
            throw new Exception("Expected at least one column");
        }

        $formatted_columns = [];

        if ($columns['id'] == null) {
            array_push($formatted_columns, "id integer primary key");
        }

        foreach ($columns as $key => $value) {
            array_push($formatted_columns, "$key $value");
        }

        $formatted_columns = join(", ", $formatted_columns);
        $statement = "create table $name ($formatted_columns)";

        return $this->execute($statement);
    }

    /**
     * == CREATE
     * This method takes as first argument an hash table which contains
     * all table's columns and respective values.
     *
     * The function automatically add single quote to string values.
     * @example
     *   $crud->into("users")->insert(["name" => "Dom", "age" => 16])
     */

    public function insert($columns) {
        $columns_keys = [];
        $columns_values = [];

        /* Raise an exception if no columns are defined */
        if (empty($columns) == 1) {
            throw new Exception("Expected at least one column");
        }

        /* Raise an exception if table is not defined */
        if ($this->table == null) {
            throw new Exception("The target table isn't defined.");
        }

        foreach ($columns as $key => $value) {
            if (gettype($value) == "string") {
                $value = "'$value'";
            }

            array_push($columns_keys, $key);
            array_push($columns_values, $value);
        }

        $columns_keys = join(", ", $columns_keys);
        $columns_values = join(", ", $columns_values);

        $statement = "insert into $this->table ($columns_keys) values($columns_values)";

        return $this->execute($statement);
    }

    /**
     * == READ
     * This method return all records from a table and pass
     * each record to an optional callback function.
     * @example
     *   $counter = 1
     *   $db->from("users")->selectAll(function($row){ $counter++; });
     */

    public function selectAll($callback = null) {
        /* Raise an exception if table is not defined */
        if ($this->table == null) {
            throw new Exception("The target table isn't defined.");
        }

        return $this->execute("select * from $this->table", $callback);
    }

    /**
     * == READ
     * This method act like a classic SELECT statement.
     * The first argument takes the list of columns to return per record,
     * if not specified it return all the columns.
     * @example
     *   $db->from("users")->where("sex='male'")->select(['id'], function($row){
     *     print $row['id'] . "\n";
     *   });
     */

    public function select($columns, $callback = null) {
        if ($columns == []) {
            array_push($columns, "*");
        }

        $columns = join(", ", $columns);
        $statement = "select $columns from $this->table";

        if ($this->where_condition != null || $this->where_condition != "") {
            $statement .= " where $this->where_condition";
        }

        return $this->execute($statement, $callback);
    }

    /**
     * == DELETE
     * This method takes as argument the table's name and destroy it
     * @example
     *   $crud->destroy("users");
     */

    public function destroy($name) {
        return $this->execute("drop table $name");
    }
}
