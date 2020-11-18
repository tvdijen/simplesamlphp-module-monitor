<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\State;

/**
 * Tests for TestCase\Cert\Data and TestCase\Cert\File
 */
class TestCertificatesTest extends \PHPUnit\Framework\TestCase
{
    /** @var string */
    private static $certdir;

    /** @var string */
    private static $key;

    public static function setUpBeforeClass(): void
    {
        self::$certdir = getcwd() . '/vendor/simplesamlphp/simplesamlphp-test-framework/certificates/rsa-pem';
        self::$key = self::$certdir . '/selfsigned.simplesamlphp.org_nopasswd.key';
    }

    public function testCertExpired(): void
    {
        $certFile = self::$certdir . '/expired.simplesamlphp.org.crt';
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

    public function testCertAboutToExpire(): void
    {
        $certFile = self::$certdir . '/signed.simplesamlphp.org.crt';
        $certData = file_get_contents($certFile);
        $certInfo = openssl_x509_parse($certData);

        // Calculate the remaining days for the cert
        $exp = (int)(($certInfo['validTo_time_t'] - time()) / 86400);

        $testData = new TestData([
            'category' => 'Test certificate',
            'certData' => $certData,
            'certExpirationWarning' => $exp + 10,
        ]);

        $certTest = new TestCase\Cert\Data($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');

        // Test that remaining days-4 = greater than $expiration, but less than $expiration+10
        $this->assertGreaterThanOrEqual($exp - 4, $expiration);
        $this->assertEquals(State::WARNING, $testResult->getState());
    }

    public function testCertFileValid(): void
    {
        $testData = new TestData([
            'category' => 'Test certificate',
            'certFile' => self::$certdir . '/selfsigned.simplesamlphp.org.crt',
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\File($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');
        $this->assertGreaterThanOrEqual(99, $expiration);
        $this->assertEquals(State::OK, $testResult->getState());
    }
}
