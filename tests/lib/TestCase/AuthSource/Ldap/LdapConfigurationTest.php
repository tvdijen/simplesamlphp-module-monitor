<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Configuration as ApplicationConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;

/**
 * Tests for TestCase\Ldap\Configuration
 */
class TestLdapConfigurationTest extends \PHPUnit\Framework\TestCase
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
