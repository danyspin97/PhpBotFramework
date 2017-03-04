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
    public function testDnsString()
    {
        $response = PhpBotFramework\Database\Database::getDns([
            'adapter' => 'pgsql',
            'host' => 'lh',
            'dbname' => 'test'
        ]);

        $this->assertEquals($response, 'pgsql:host=lh;dbname=test');
    }

    public function testShouldBePresentDefaultAdapter()
    {
        $params = PhpBotFramework\Database\Database::addDefaultValue([ 'dbname' => 'test' ]);
        $response = PhpBotFramework\Database\Database::getDns($params);

        $this->assertEquals($response, 'mysql:host=localhost;dbname=test');
    }
}
