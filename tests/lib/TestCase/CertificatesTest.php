<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for TestCase\Cert\Data and TestCase\Cert\File
 */
class TestCertificatesTest extends \PHPUnit_Framework_TestCase
{
    private static $certdir;
    private static $key;

    public static function setUpBeforeClass()
    {
        self::$certdir = getcwd().DIRECTORY_SEPARATOR.'vendor/simplesamlphp/simplesamlphp-test-framework/certificates/pem';
        self::$key = self::$certdir.DIRECTORY_SEPARATOR.'selfsigned.example.org_nopasswd.key';
    }

    public function testCertExpired()
    {
        $certFile = self::$certdir.DIRECTORY_SEPARATOR.'expired.example.org.crt';
        $cert = file_get_contents($certFile);

        $testData = new TestData([
            'category' => 'Test certificate',
            'certData' => $cert,
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\Data($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');
        $this->assertLessThanOrEqual(-1, $expiration);
        $this->assertEquals(State::ERROR, $testResult->getState());
    }

    public function testCertAboutToExpire()
    {
        $certFile = self::$certdir.DIRECTORY_SEPARATOR.'signed.example.org.crt';
        $certData = file_get_contents($certFile);
        $certInfo = openssl_x509_parse($certData);

        // Calculate the remaining days for the cert
        $exp = (int)(($certInfo['validTo_time_t'] - time()) / 86400);

        $testData = new TestData([
            'category' => 'Test certificate',
            'certData' => $certData,
            'certExpirationWarning' => $exp+10,
        ]);

        $certTest = new TestCase\Cert\Data($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');

        // Test that remaining days-4 = greater than $expiration, but less than $expiration+10
        $this->assertGreaterThanOrEqual($exp, $expiration + 4);
        $this->assertEquals(State::WARNING, $testResult->getState());
    }

    public function testCertFileValid()
    {
        $testData = new TestData([
            'category' => 'Test certificate',
            'certFile' => self::$certdir.DIRECTORY_SEPARATOR.'selfsigned.example.org.crt',
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\File($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');
        $this->assertGreaterThanOrEqual(99, $expiration);
        $this->assertEquals(State::OK, $testResult->getState());
    }
}
