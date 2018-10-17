<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for MemcacheServer
 */
class TestMemcacheServerTest extends \PHPUnit_Framework_TestCase
{
    public function testMemcacheServerUp()
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

    public function testMemcacheServerDown()
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
