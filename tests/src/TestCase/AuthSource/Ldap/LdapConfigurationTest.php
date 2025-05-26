<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;

/**
 * Tests for TestCase\Ldap\Configuration
 */
final class TestLdapConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testLdapConfiguration(): void
    {
        $authSourceData = [
            'connection_string' => 'ldaps://ldap.example.com:636',
            'debug' => false,
        ];

        $confTest = new TestCase\AuthSource\Ldap\Configuration(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
            ]),
        );

        $testResult = $confTest->getTestResult();
        $this->assertEquals('ldaps://ldap.example.com:636', $testResult->getSubject());
    }
}
