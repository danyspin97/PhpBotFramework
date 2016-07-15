<?php
    namespace HadesSQL;

    class CRUD
    {
    	public function __construct()
    	{

    	}

    	/**
    	 * == CREATE
    	 * This function takes as first argument the table's name and 
    	 * as second argument an hash table which contains all table's columns
    	 * @example
    	 *   $crud->createTable("user", ["name" => "text", "money" => "real"])
    	 */

    	public function createTable($name, $columns)
    	{
    		/* Raise an exception if no columns are defined */
    		if(empty($columns) == 1)
    		    throw new Exception("Expected at least one column");

    		$formatted_columns = ["id integer primary key"];

    		foreach($columns as $key => $value)
    			array_push($formatted_columns, "$key $value");

    	    $formatted_columns = join(", ", $formatted_columns);
    		$statement = "create table $name values($formatted_columns)";

    		return $db->execute($statement);
    	}
    
    $crud = new CRUD();
?>