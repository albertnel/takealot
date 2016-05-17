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
    /**
    *   Tests a single valid IP address.
    */
    public function testValidCheckIpAddress()
    {
        $ip = '127.0.0.1';
        $valid = checkIpAddress($ip);
        $this->assertTrue($valid);
    }

    /**
    *   Tests a single invalid IP address.
    */
    public function testInvalidCheckIpAddress()
    {
        $ip = 'what.ip.address';
        $valid = checkIpAddress($ip);
        $this->assertFalse($valid);
    }

    /**
    *   Tests a list of valid and invalid IP address.
    */
    public function testValidCheckIpAddressList()
    {
        $all_ips = [
            '196.14.118.196',
            '192.168.10.10',
            '192.168.10.4',
            '192.168.takealot.com',
            '192.168.255.255',
            '127.0.0.1',
            'http://thisdomainexists.com',
            '107.44.399.120',
            '107.44.201.120',
            'My dog used to chase people on a bike a lot. It got so bad, finally I had to take his bike away.'
        ];

        $valid_ips = [
            '107.44.201.120',
            '127.0.0.1',
            '192.168.10.4',
            '192.168.10.10',
            '192.168.255.255',
            '196.14.118.196'
        ];

        $invalid_ips = [
            '107.44.399.120',
            '192.168.takealot.com',
            'My dog used to chase people on a bike a lot. It got so bad, finally I had to take his bike away.',
            'http://thisdomainexists.com'
        ];

        $input_file = __DIR__ . '/../../public/output/test_ip_addresses.txt';
        $fh = fopen($input_file, 'w');

        foreach ($all_ips as $ip) {
            fwrite($fh, $ip . "\n");
        }
        fclose($fh);

        $out_file_valid = 'test_valid_ips.txt';
        $out_file_invalid = 'test_invalid_ips.txt';

        $app = new AppController();
        $result = $app->processFile($input_file, $out_file_valid, $out_file_invalid);

        // Check if file was processed successfully
        $this->assertTrue($result);

        // Check if $out_file_valid contains correct entries
        $counter = 0;
        $fh = fopen(__DIR__ . '/../../public/output/' . $out_file_valid, 'r');
        while (!feof($fh)) {
            $line = trim(fgets($fh));
            if (!empty($line)) {
                $this->assertEquals($line, $valid_ips[$counter]);
                $counter++;
            }
        }
        fclose($fh);

        // Check if $out_file_invalid contains correct entries
        $counter = 0;
        $fh = fopen(__DIR__ . '/../../public/output/' . $out_file_invalid, 'r');
        while (!feof($fh)) {
            $line = trim(fgets($fh));
            if (!empty($line)) {
                $this->assertEquals($line, $invalid_ips[$counter]);
                $counter++;
            }
        }
        fclose($fh);

        unlink($input_file);
        unlink(__DIR__ . '/../../public/output/' . $out_file_valid);
        unlink(__DIR__ . '/../../public/output/' . $out_file_invalid);
    }
}

?>
