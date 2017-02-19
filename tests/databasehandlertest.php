<?php

require './vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class DatabaseHandlerTest extends TestCase
{

    use PhpBotFramework\Database\Handler;

    /*
     * stringify($params)
     * Taken an associate array, transform it in a string so it 
     * could be passed to PDO in order to connect to a database.
     */
    public function testDndString()
    {
        $response = $this->getDns([
            'adapter' => 'pgsql',
            'host' => 'lh',
            'dbname' => 'test'
        ]);

        $this->assertEquals($response, 'pgsql:host=lh;dbname=test');
    }

    public function testShouldBePresentDefaultAdapter()
    {
        $params = $this->addDefaultValue([ 'dbname' => 'test' ]);
        $response = $this->getDns($params);

        $this->assertEquals($response, 'mysql:host=localhost;dbname=test');
    }
}
