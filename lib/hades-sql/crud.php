<?php
    class CRUD
    {
    	/**
    	 * == CREATE
    	 * This method takes as first argument the table's name and 
    	 * as second argument an hash table which contains all table's columns
    	 * @example
    	 *   $crud->createTable("user", ["name" => "text", "money" => "real"])
    	 */

    	public function createTable($name, $columns)
    	{
    		/* Raise an exception if no columns are defined */
    		if(empty($columns) == 1)
    		    throw new Exception("Expected at least one column");

            $formatted_columns = [];

            if($columns['id'] == NULL)
    		  array_push($formatted_columns, "id integer primary key");

    		foreach($columns as $key => $value)
    			array_push($formatted_columns, "$key $value");

    	    $formatted_columns = join(", ", $formatted_columns);
    		$statement = "create table $name ($formatted_columns)";

    		return $this->execute($statement);
    	}

        /**
         * == CREATE
         * This method takes as first argument the table's name and 
         * as second arguments an hash table which contains all table's columns
         * and respective values.
         *
         * The function automatically add single quote to string values.
         * @example
         *   $crud->insertInto("users", ["name" => "Dom", "age" => 16])
         */

        public function insertInto($name, $columns)
        {
            $columns_keys = [];
            $columns_values = [];

            /* Raise an exception if no columns are defined */
            if(empty($columns) == 1)
                throw new Exception("Expected at least one column");

            foreach($columns as $key => $value)
            {
                if(gettype($value) == "string")
                  $value = "'$value'";

                array_push($columns_keys, $key);
                array_push($columns_values, $value);
            }

            $columns_keys = join(", ", $columns_keys);
            $columns_values = join(", ", $columns_values);

            $statement = "insert into $name ($columns_keys) values($columns_values)";

            return $this->execute($statement);
        }

        /**
         * == DELETE
         * This method takes as argument the table's name and destroy it
         * @example
         *   $crud->destroy("users");
         */

        public function destroy($name)
        {
            return $this->execute("drop table $name");
        }
    }
?>