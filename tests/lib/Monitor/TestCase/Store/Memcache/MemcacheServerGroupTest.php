<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\State as State;

/**
 * Tests for MemcacheServerGroup
 */
class TestMemcacheServerGroupTest extends \PHPUnit_Framework_TestCase
{
    public function testMemcacheServerGroup()
    {
        $a = new TestResult();
        $a->setState(State::OK);

        $b = new TestResult();
        $b->setState(State::ERROR);

        $testData = new TestData([
            'results' => array($a, $a),
        ]);
        $testCase = new TestCase\Store\Memcache\ServerGroup($testData);
        $testResult = $testCase->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());

        $testData = new TestData([
            'results' => array($a, $b),
        ]);
        $testCase = new TestCase\Store\Memcache\ServerGroup($testData);
        $testResult = $testCase->getTestResult();
        $this->assertEquals(State::WARNING, $testResult->getState());

        $testData = new TestData([
            'results' => array($b, $b),
        ]);
        $testCase = new TestCase\Store\Memcache\ServerGroup($testData);
        $testResult = $testCase->getTestResult();
        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
