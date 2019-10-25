<?php

namespace SimpleSAML\Module\Monitor\Test;

use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\State;

/**
 * Tests for TestCase\Network\ConnectUri
 */
class TestConnectUriTest extends \PHPUnit\Framework\TestCase
{
    public function testConnectUriOK(): void
    {
        $testData = new TestData([
            'uri' => 'ssl://127.0.0.1:443',
            'context' => stream_context_create([
                "ssl" => [
                    "capture_peer_cert" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false
                ]
            ]),
        ]);
        $connectionTest = new TestCase\Network\ConnectUri($testData);
        $testResult = $connectionTest->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testConnectUriFailed(): void
    {
        $testData = new TestData([
            'uri' => 'ssl://127.0.0.1:442',
        ]);
        $connectionTest = new TestCase\Network\ConnectUri($testData);
        $testResult = $connectionTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
