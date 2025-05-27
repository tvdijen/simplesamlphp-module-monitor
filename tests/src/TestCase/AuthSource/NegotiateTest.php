<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\Test;

use Exception;
use KRB5NegotiateAuth;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;

/**
 * Tests for TestCase\Negotiate
 */
#[RequiresPhpExtension('krb5')]
final class TestNegotiateTest extends \PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        $_SERVER['SERVER_NAME'] = 'localhost';
    }

    public static function tearDownAfterClass(): void
    {
        unset($_SERVER['SERVER_NAME']);
    }

    public function testNegotiateException(): void
    {
        $KRB5NegotiateAuthMock = $this->getMockBuilder(KRB5NegotiateAuth::class)->onlyMethods(
            ['doAuthentication', 'getAuthenticatedUser'],
        )->disableOriginalConstructor()->getMock();
        $KRB5NegotiateAuthMock->expects($this->any())->method('doAuthentication')->will(
            $this->throwException(new Exception('Generic exception message')),
        );
        $testData = new TestData([
            'handle' => $KRB5NegotiateAuthMock,
        ]);

        $confTest = new TestCase\AuthSource\Negotiate($testData);
        $testResult = $confTest->getTestResult();
        $this->assertEquals(State::WARNING, $testResult->getState());
    }

    public function testNegotiateSuccess(): void
    {
        $KRB5NegotiateAuthMock = $this->getMockBuilder(KRB5NegotiateAuth::class)->onlyMethods(
            ['doAuthentication', 'getAuthenticatedUser'],
        )->disableOriginalConstructor()->getMock();
        $KRB5NegotiateAuthMock->expects($this->any())->method('doAuthentication')->willReturn(true);
        $KRB5NegotiateAuthMock->expects($this->any())
            ->method('getAuthenticatedUser')
            ->willReturn('testuser@example.org');
        $testData = new TestData([
            'handle' => $KRB5NegotiateAuthMock,
        ]);

        $confTest = new TestCase\AuthSource\Negotiate($testData);
        $testResult = $confTest->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testNegotiateNoAuthorzation(): void
    {
        $KRB5NegotiateAuthMock = $this->getMockBuilder(KRB5NegotiateAuth::class)->onlyMethods(
            ['doAuthentication', 'getAuthenticatedUser'],
        )->disableOriginalConstructor()->getMock();
        $KRB5NegotiateAuthMock->expects($this->any())->method('doAuthentication')->willReturn(false);
        $testData = new TestData([
            'handle' => $KRB5NegotiateAuthMock,
        ]);

        $confTest = new TestCase\AuthSource\Negotiate($testData);
        $testResult = $confTest->getTestResult();
        $this->assertEquals(State::SKIPPED, $testResult->getState());
    }

    public function testNegotiateError(): void
    {
        $KRB5NegotiateAuthMock = $this->getMockBuilder(KRB5NegotiateAuth::class)->onlyMethods(
            ['doAuthentication', 'getAuthenticatedUser'],
        )->disableOriginalConstructor()->getMock();
        $KRB5NegotiateAuthMock->expects($this->any())->method('doAuthentication')->willReturn(false);
        $testData = new TestData([
            'handle' => $KRB5NegotiateAuthMock,
            'authorization' => 'test',
        ]);

        $confTest = new TestCase\AuthSource\Negotiate($testData);
        $testResult = $confTest->getTestResult();
        $this->assertEquals(State::WARNING, $testResult->getState());
    }
}
