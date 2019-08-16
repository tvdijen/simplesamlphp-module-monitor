<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for MemcacheServerGroup
 */
class TestMemcacheServerGroupTest extends \PHPUnit\Framework\TestCase
{
    public function testMemcacheServerGroup()
    {
        $a = new TestResult();
        $a->setState(State::OK);

        $b = new TestResult();
        $b->setState(State::ERROR);

        $testData = new TestData([
            'results' => [$a, $a],
        ]);
        $testCase = new TestCase\Store\Memcache\ServerGroup($testData);
        $testResult = $testCase->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());

        $testData = new TestData([
            'results' => [$a, $b],
        ]);
        $testCase = new TestCase\Store\Memcache\ServerGroup($testData);
        $testResult = $testCase->getTestResult();
        $this->assertEquals(State::WARNING, $testResult->getState());

        $testData = new TestData([
            'results' => [$b, $b],
        ]);
        $testCase = new TestCase\Store\Memcache\ServerGroup($testData);
        $testResult = $testCase->getTestResult();
        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
