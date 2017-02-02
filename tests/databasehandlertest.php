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
    public function testStringify()
    {

        $response = $this->stringify([
            'adapter' => 'pgsql',
            'host' => 'lh',
            'dbname' => 'test'
        ]);

        $this->assertEquals($response, 'pgsql:host=lh;dbname=test');
        return;
    }

    public function testShouldBePresentDefaultAdapter()
    {
        $params = $this->mergeWithDefaults([ 'dbname' => 'test' ]);
        $response = $this->stringify($params);

        $this->assertEquals($response, 'mysql:host=localhost;dbname=test');
        return;
    }
}
