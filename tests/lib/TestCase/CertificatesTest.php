<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

/**
 * Tests for DependencyInjection
 */
class TestCertificatesTest extends \PHPUnit_Framework_TestCase
{
    public function testCertData()
    {
        $testData = new TestData([
            'category' => 'Test certificate',
            'certData' => file_get_contents(dirname(__FILE__) . '/../../files/example.org.crt'),
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\Data($testData);

        $this->assertEquals($testData, $certTest->getTestData());
        $this->assertEquals('Test certificate', $certTest->getCategory());
        $this->assertEquals('www.surfnet.nl', $certTest->getSubject());
    }

    public function testCertFile()
    {
        $testData = new TestData([
            'category' => 'Test certificate',
            'certFile' => dirname(__FILE__) . '/../../files/example.org.crt',
            'certExpirationWarning' => 10,
        ]);
        $certTest = new TestCase\Cert\File($testData);

        $this->assertEquals($testData, $certTest->getTestData());
        $this->assertEquals('Test certificate', $certTest->getCategory());
        $this->assertEquals('www.surfnet.nl', $certTest->getSubject());
    }
}
