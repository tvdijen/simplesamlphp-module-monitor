<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Module\monitor\State;

/**
 * Tests for MemcacheServer
 */
class TestMemcacheServerTest extends \PHPUnit\Framework\TestCase
{
    public function testMemcacheServerUp(): void
    {
        $testData = new TestData([
            'serverStats' => ['bytes' => 1024, 'limit_maxbytes' => 2048],
            'host' => 'testhost.example.org',
        ]);
        $testCase = new TestCase\Store\Memcache\Server($testData);

        $testResult = $testCase->getTestResult();
        $testOutput = $testResult->getOutput();

        $this->assertEquals(State::OK, $testResult->getState());
        $this->assertEquals(50, $testOutput['freePercentage']);
    }

    public function testMemcacheServerDown(): void
    {
        $testData = new TestData([
            'serverStats' => false,
            'host' => 'testhost.example.org',
        ]);
        $testCase = new TestCase\Store\Memcache\Server($testData);

        $testResult = $testCase->getTestResult();
        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
