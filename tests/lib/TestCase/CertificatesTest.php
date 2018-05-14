<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\State as State;

/**
 * Tests for TestCase\Cert\Data and TestCase\Cert\File
 */
class TestCertificatesTest extends \PHPUnit_Framework_TestCase
{
    private static $key;

    private static $dn;

    public static function setUpBeforeClass()
    {
        self::$key = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => '1024',
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        self::$dn = [
            'countryName' => 'NL',
            'localityName' => 'Amsterdam',
            'organizationName' => 'TestOrganization',
        ];
    }

    public static function tearDownAfterClass()
    {
        self::$key = null;
        self::$dn = null;
        unlink(sys_get_temp_dir().'/validcert.crt');
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
        $this->assertEquals(-10, $expiration);
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
        $this->assertEquals(5, $expiration);
        $this->assertEquals(State::WARNING, $testResult->getState());
    }

    public function testCertFileValid()
    {
        $dn = self::$dn;
        $dn['commonName'] = 'valid';

        $csr = openssl_csr_new($dn, $key, ['digest_alg' => 'sha256']);
        $res = openssl_csr_sign($csr, null, $key, $days = 100, ['digest_alg' => 'sha256']);
        openssl_x509_export($res, $cert);

        $certFile = sys_get_temp_dir().'/validcert.crt';
        file_put_contents($certFile, $cert);

        $testData = new TestData([
            'category' => 'Test certificate',
            'certFile' => $certFile,
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\File($testData);
        $testResult = $certTest->getTestResult();
        $expiration = $testResult->getOutput('expiration');
        $this->assertEquals(100, $expiration);
        $this->assertEquals(State::OK, $testResult->getState());
    }
}
