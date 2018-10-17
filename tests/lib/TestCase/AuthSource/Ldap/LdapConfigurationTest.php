<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML_Configuration as ApplicationConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

/**
 * Tests for TestCase\Ldap\Configuration
 */
class TestLdapConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testLdapConfiguration()
    {
        $authSourceData = [
            'hostname' => 'ldaps://ldap.example.com:636',
            'debug' => false,
        ];

        $confTest = new TestCase\AuthSource\Ldap\Configuration(
            new TestData([
                'authSourceData' => ApplicationConfiguration::loadFromArray($authSourceData),
            ])
        );

        $testResult = $confTest->getTestResult();
        $this->assertEquals('ldaps://ldap.example.com:636', $testResult->getSubject());

        $authSourceData = [
            'hostname' => 'ldap.example.com',
            'port' => 636,
            'enable_tls' => true,
            'timeout' => 999,
            'debug' => false,
            'referrals' => true,
        ];

        $confTest = new TestCase\AuthSource\Ldap\Configuration(
            new TestData(['authSourceData' => ApplicationConfiguration::loadFromArray($authSourceData)])
        );

        $testResult = $confTest->getTestResult();
        $this->assertEquals('ldap.example.com:636', $testResult->getSubject());
    }
}