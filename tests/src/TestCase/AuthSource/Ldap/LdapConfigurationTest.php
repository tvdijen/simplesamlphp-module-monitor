<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;

/**
 * Tests for TestCase\Ldap\Configuration
 */
class TestLdapConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testLdapConfiguration(): void
    {
        $authSourceData = [
            'hostname' => 'ldaps://ldap.example.com:636',
            'debug' => false,
        ];

        $confTest = new TestCase\AuthSource\Ldap\Configuration(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
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
            new TestData(['authSourceData' => Configuration::loadFromArray($authSourceData)])
        );

        $testResult = $confTest->getTestResult();
        $this->assertEquals('ldap.example.com:636', $testResult->getSubject());
    }
}
