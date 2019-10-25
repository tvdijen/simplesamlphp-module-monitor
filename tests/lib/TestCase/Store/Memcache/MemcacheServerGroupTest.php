<?php

namespace SimpleSAML\Module\Monitor\Test;

use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\State;

/**
 * Tests for MemcacheServerGroup
 */
class TestMemcacheServerGroupTest extends \PHPUnit\Framework\TestCase
{
    public function testMemcacheServerGroup(): void
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
