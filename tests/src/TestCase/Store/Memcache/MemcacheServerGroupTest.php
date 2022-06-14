<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Module\monitor\State;

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
