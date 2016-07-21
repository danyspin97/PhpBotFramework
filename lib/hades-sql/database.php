<?php

    class Database
    {
    	protected $pdo;
        protected Bot $bot;

    	public function __construct($driver, $dbname, $user, $password)
    	{
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

    	public function execute($query, $callback=NULL)
    	{
    		$statement = $this->pdo->query($query);

    		/** Users can avoid to specify a callback function */
    		if($callback != NULL)
    		    while($row = $statement->fetch(PDO::FETCH_ASSOC))
    			    $callback($row);

    		return 0;
    	}
        
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

        public function insertInto($table_name, $columns)
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

                array_push($columns, $key);
                array_push($columns_values, $value);
            }

            $columns = join(", ", $columns);
            $columns_values = join(", ", $columns_values);

            $statement = "insert into $name ($columns) values($columns_values)";
            
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