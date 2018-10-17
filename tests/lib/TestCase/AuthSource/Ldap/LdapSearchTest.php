<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML_Configuration as ApplicationConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for TestCase\Ldap\Search
 */
class TestLdapSearchTest extends \PHPUnit_Framework_TestCase
{
    public function testSearchSuccesful()
    {
        $authSourceData = [
            'search.base' => 'OU=example,DC=example,DC=org',
            'search.username' => 'testuser',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMock('LdapSearch', array('searchfordn'));
        $connectionMock->expects($this->once())->method('searchfordn')->will($this->returnValue(true));
        $confTest = new TestCase\AuthSource\Ldap\Search(
            new TestData([
                'authSourceData' => ApplicationConfiguration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testSearchFailed()
    {
        $authSourceData = [
            'search.base' => 'OU=example,DC=example,DC=org',
            'search.username' => 'CN=testuser,OU=example,DC=example,DC=org',
            'search.password' => 'password',
        ];
        $connectionMock = $this->getMock('LdapSearch', array('searchfordn'));
        $connectionMock->expects($this->once())->method('searchfordn')->will($this->throwException(new \SimpleSAML_Error_Error('UNHANDLEDEXCEPTION')));
        $confTest = new TestCase\AuthSource\Ldap\Search(
            new TestData([
                'authSourceData' => ApplicationConfiguration::loadFromArray($authSourceData),
                'connection' => $connectionMock,
            ])
        );
        $testResult = $confTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
