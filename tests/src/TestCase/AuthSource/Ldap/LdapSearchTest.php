<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Module\ldap\Connector\Ldap;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use Symfony\Component\Ldap\Entry;

/**
 * Tests for TestCase\Ldap\Search
 */
final class TestLdapSearchTest extends \PHPUnit\Framework\TestCase
{
    public function testSearchSuccesful(): void
    {
        $authSourceData = [
            'search.base' => 'OU=example,DC=example,DC=org',
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];
        $entry = new Entry(
            'DN=testuser,OU=example,DC=example,DC=org',
            ['firstName' => ['John'], 'lastName' => ['Doe']],
        );

        $connectionMock = $this->getMockBuilder(Ldap::class)->onlyMethods(
            ['search'],
        )->disableOriginalConstructor()->getMock();
        $connectionMock->expects($this->once())->method('search')->willReturn($entry);
        $confTest = new TestCase\AuthSource\Ldap\Search(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ]),
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
            ['search'],
        )->disableOriginalConstructor()->getMock();
        $connectionMock->expects($this->once())->method('search')->will(
            $this->throwException(new Error\Error('UNHANDLEDEXCEPTION')),
        );
        $confTest = new TestCase\AuthSource\Ldap\Search(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ]),
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
