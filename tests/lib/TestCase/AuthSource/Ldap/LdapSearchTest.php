<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Module\ldap\Auth\Ldap;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\State;

/**
 * Tests for TestCase\Ldap\Search
 */
class TestLdapSearchTest extends \PHPUnit\Framework\TestCase
{
    public function testSearchSuccesful(): void
    {
        $authSourceData = [
            'search.base' => 'OU=example,DC=example,DC=org',
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];

        $connectionMock = $this->getMockBuilder(Ldap::class)->onlyMethods(
            ['searchfordn']
        )->disableOriginalConstructor()->getMock();
        $connectionMock->expects($this->once())->method('searchfordn')->will($this->returnValue('testDN'));
        $confTest = new TestCase\AuthSource\Ldap\Search(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testSearchFailed(): void
    {
        $authSourceData = [
            'search.base' => 'OU=example,DC=example,DC=org',
            'search.username' => 'CN=testuser,OU=example,DC=example,DC=org',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMockBuilder(Ldap::class)->onlyMethods(
            ['searchfordn']
        )->disableOriginalConstructor()->getMock();
        $connectionMock->expects($this->once())->method('searchfordn')->will(
            $this->throwException(new \SimpleSAML\Error\Error('UNHANDLEDEXCEPTION'))
        );
        $confTest = new TestCase\AuthSource\Ldap\Search(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
