<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class DatabaseHandlerTest extends TestCase
{
    /*
     * stringify($params)
     * Taken an associate array, transform it in a string so it 
     * could be passed to PDO in order to connect to a database.
     */
    /*public function testDndString()
    {
        $database = new PhpBotFramework\Database\Database;
        $response = $database->getDns([
            'adapter' => 'pgsql',
            'host' => 'lh',
            'dbname' => 'test'
        ]);

        $this->assertEquals($response, 'pgsql:host=lh;dbname=test');
    }

    public function testShouldBePresentDefaultAdapter()
    {
        $database = new PhpBotFramework\Database\Database;
        $params = $this->addDefaultValue([ 'dbname' => 'test' ]);
        $response = $this->getDns($params);

        $this->assertEquals($response, 'mysql:host=localhost;dbname=test');
    }*/
}
