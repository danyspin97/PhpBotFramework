<?php
    include "crud.php";

    class Database extends CRUD
    {
    	public $pdo;

    	public function __construct($dbname, $user, $password)
    	{
    		$this->pdo = new PDO("pgsql:host=localhost;dbname=$dbname", $user, $password);
    		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    		$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
    }
?>