<?php

/**
*   AppTest class.
*
*   Extends the PHPUnit unit testing library.
*   Runs tests on the validation method.
*
*   @author Albert Nel
*/

/**
*   Include other application classes and files.
*/
require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../AppController.php';

class AppTest extends PHPUnit_Framework_TestCase
{
    public function testValidCheckIpAddress()
    {
        $ip = '127.0.0.1';
        $valid = checkIpAddress($ip);
        $this->assertTrue($valid);
    }

    public function testInvalidCheckIpAddress()
    {
        $ip = 'what.ip.address';
        $valid = checkIpAddress($ip);
        $this->assertFalse($valid);
    }
}

?>
