<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Module\ldap\Auth\Ldap;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\State;

/**
 * Tests for TestCase\Ldap\Bind
 */
class TestLdapBindTest extends \PHPUnit\Framework\TestCase
{
    public function testBindSuccesful(): void
    {
        $authSourceData = [
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMockBuilder(Ldap::class)->onlyMethods(
            ['bind']
        )->disableOriginalConstructor()->getMock();
        $connectionMock->expects($this->once())->method('bind')->will($this->returnValue(true));
        $confTest = new TestCase\AuthSource\Ldap\Bind(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testBindFailed(): void
    {
        $authSourceData = [
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMockBuilder(Ldap::class)->onlyMethods(
            ['bind']
        )->disableOriginalConstructor()->getMock();
        $connectionMock->expects($this->once())->method('bind')->will(
            $this->throwException(new \Exception())
        );
        $confTest = new TestCase\AuthSource\Ldap\Bind(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::FATAL, $testResult->getState());
    }

    public function testAuthenticationFailed(): void
    {
        $authSourceData = [
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMockBuilder(Ldap::class)->onlyMethods(
            ['bind']
        )->disableOriginalConstructor()->getMock();
        $connectionMock->expects($this->once())->method('bind')->will($this->returnValue(false));
        $confTest = new TestCase\AuthSource\Ldap\Bind(
            new TestData([
                'authSourceData' => Configuration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
