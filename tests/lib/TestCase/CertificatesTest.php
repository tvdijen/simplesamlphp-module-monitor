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
    private static $certdir = dirname('../../../vendor/simplesamlphp/simplesamlphp-test-framework/certificates/pem');
    private static $key = $certdir.DIRECTORY_SEPARATOR.'selfsigned.example.org_nopasswd.key';

    private static $dn;

    public static function setUpBeforeClass()
    {
        self::$dn = [
            'countryName' => 'NL',
            'localityName' => 'Amsterdam',
            'organizationName' => 'TestOrganization',
        ];
    }

    public function testCertExpired()
    {
        $dn = self::$dn;
        $dn['commonName'] = 'expired';

        $csr = openssl_csr_new($dn, $key, ['digest_alg' => 'sha256']);
        $res = openssl_csr_sign($csr, null, $key, $days = -10, ['digest_alg' => 'sha256']);
        openssl_x509_export($res, $cert);

        $testData = new TestData([
            'category' => 'Test certificate',
            'certData' => $cert,
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\Data($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');
        $this->assertLessThanOrEqual(-10, $expiration);
        $this->assertEquals(State::ERROR, $testResult->getState());
    }

    public function testCertAboutToExpire()
    {
        $dn = self::$dn;
        $dn['commonName'] = 'almostexpired';

        $csr = openssl_csr_new($dn, $key, ['digest_alg' => 'sha256']);
        $res = openssl_csr_sign($csr, null, $key, $days = 5, ['digest_alg' => 'sha256']);
        openssl_x509_export($res, $cert);

        $testData = new TestData([
            'category' => 'Test certificate',
            'certData' => $cert,
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\Data($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');
        $this->assertGreaterThanOrEqual(4, $expiration);
        $this->assertEquals(State::WARNING, $testResult->getState());
    }

    public function testCertFileValid()
    {
        $testData = new TestData([
            'category' => 'Test certificate',
            'certFile' => $certdir.DIRECTORY_SEPARATOR.'selfsigned.example.org.crt',
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\File($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');
        $this->assertGreaterThanOrEqual(99, $expiration);
        $this->assertEquals(State::OK, $testResult->getState());
    }
}
