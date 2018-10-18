<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Configuration as ApplicationConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for TestCase\Ldap\Bind
 */
class TestLdapBindTest extends \PHPUnit_Framework_TestCase
{
    public function testBindSuccesful()
    {
        $authSourceData = [
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMock('LdapConnection', array('bind'));
        $connectionMock->expects($this->once())->method('bind')->will($this->returnValue(true));
        $confTest = new TestCase\AuthSource\Ldap\Bind(
            new TestData([
                'authSourceData' => ApplicationConfiguration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testBindFailed()
    {
        $authSourceData = [
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMock('LdapConnection', array('bind'));
        $connectionMock->expects($this->once())->method('bind')->will($this->throwException(new \Exception()));
        $confTest = new TestCase\AuthSource\Ldap\Bind(
            new TestData([
                'authSourceData' => ApplicationConfiguration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::FATAL, $testResult->getState());
    }

    public function testAuthenticationFailed()
    {
        $authSourceData = [
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMock('LdapConnection', array('bind'));
        $connectionMock->expects($this->once())->method('bind')->will($this->returnValue(false));
        $confTest = new TestCase\AuthSource\Ldap\Bind(
            new TestData([
                'authSourceData' => ApplicationConfiguration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
