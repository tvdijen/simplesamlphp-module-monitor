<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for TestCase\Network\ConnectUri
 */
class TestConnectUriTest extends \PHPUnit\Framework\TestCase
{
    public function testConnectUriOK()
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

    public function testConnectUriFailed()
    {
        $testData = new TestData([
            'uri' => 'ssl://127.0.0.1:442',
        ]);
        $connectionTest = new TestCase\Network\ConnectUri($testData);
        $testResult = $connectionTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
