<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;

use function gethostbyname;
use function sprintf;
use function stream_context_create;

/**
 * Tests for TestCase\Network\ConnectUri
 */
final class TestConnectUriTest extends \PHPUnit\Framework\TestCase
{
    protected static string $host;

    public static function setUpBeforeClass(): void
    {
        self::$host = gethostbyname('packagist.org');
    }

    public function testConnectUriOK(): void
    {
        $testData = new TestData([
            'uri' => sprintf('ssl://%s:443', self::$host),
            'timeout' => 3,
            'context' => stream_context_create([
                "ssl" => [
                    "capture_peer_cert" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ]),
        ]);
        $connectionTest = new TestCase\Network\ConnectUri($testData);
        $testResult = $connectionTest->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testConnectUriFailed(): void
    {
        $testData = new TestData([
            'uri' => sprintf('ssl://%s:442', self::$host),
            'timeout' => 3,
        ]);
        $connectionTest = new TestCase\Network\ConnectUri($testData);
        $testResult = $connectionTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
