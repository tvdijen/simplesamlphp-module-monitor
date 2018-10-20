<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Configuration as ApplicationConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for TestCase\Negotiate
 */
class TestNegotiateTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $_SERVER['SERVER_NAME'] = 'localhost';
    }

    public static function tearDownAfterClass()
    {
        unset($_SERVER['SERVER_NAME']);
    }

    public function testNegotiateException()
    {
        $KRB5NegotiateAuthMock = $this->getMockBuilder('KRB5NegotiateAuth')->setMethods(['doAuthentication', 'getAuthenticatedUser'])->disableOriginalConstructor()->getMock();
        $KRB5NegotiateAuthMock->expects($this->any())->method('doAuthentication')->will($this->throwException(new \Exception('Generic exception message')));
        $testData = new TestData([
            'handle' => $KRB5NegotiateAuthMock,
        ]);

        $confTest = new TestCase\AuthSource\Negotiate($testData);
        $testResult = $confTest->getTestResult();
        $this->assertEquals(State::WARNING, $testResult->getState());
    }

    public function testNegotiateSuccess()
    {
        $KRB5NegotiateAuthMock = $this->getMockBuilder('KRB5NegotiateAuth')->setMethods(['doAuthentication', 'getAuthenticatedUser'])->disableOriginalConstructor()->getMock();
        $KRB5NegotiateAuthMock->expects($this->any())->method('doAuthentication')->will($this->returnValue(true));
        $KRB5NegotiateAuthMock->expects($this->any())->method('getAuthenticatedUser')->will($this->returnValue('testuser@example.org'));
        $testData = new TestData([
            'handle' => $KRB5NegotiateAuthMock,
        ]);

        $confTest = new TestCase\AuthSource\Negotiate($testData);
        $testResult = $confTest->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testNegotiateNoAuthorzation()
    {
        $KRB5NegotiateAuthMock = $this->getMockBuilder('KRB5NegotiateAuth')->setMethods(['doAuthentication', 'getAuthenticatedUser'])->disableOriginalConstructor()->getMock();
        $KRB5NegotiateAuthMock->expects($this->any())->method('doAuthentication')->will($this->returnValue(false));
        $testData = new TestData([
            'handle' => $KRB5NegotiateAuthMock,
        ]);

        $confTest = new TestCase\AuthSource\Negotiate($testData);
        $testResult = $confTest->getTestResult();
        $this->assertEquals(State::SKIPPED, $testResult->getState());
    }

    public function testNegotiateError()
    {
        $KRB5NegotiateAuthMock = $this->getMockBuilder('KRB5NegotiateAuth')->setMethods(['doAuthentication', 'getAuthenticatedUser'])->disableOriginalConstructor()->getMock();
        $KRB5NegotiateAuthMock->expects($this->any())->method('doAuthentication')->will($this->returnValue(false));
        $testData = new TestData([
            'handle' => $KRB5NegotiateAuthMock,
            'authorization' => 'test',
        ]);

        $confTest = new TestCase\AuthSource\Negotiate($testData);
        $testResult = $confTest->getTestResult();
        $this->assertEquals(State::WARNING, $testResult->getState());
    }
}
