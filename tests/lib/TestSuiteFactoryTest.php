<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestSuiteFactory as TestSuiteFactory;

/**
 * Tests for TestSuiteFactory
 */
class TestSuiteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testTestSuiteFactory()
    {
        $config = new TestConfiguration(
            [],
            [],
            \SimpleSAML_Configuration::loadFromArray(['metadata.sources' => []]),
            \SimpleSAML_Configuration::loadFromArray([]),
            \SimpleSAML_Configuration::loadFromArray([])
        );
        $testData = new TestData([]);
        $testSuite = new TestSuiteInstance($config, $testData);
        $results = $testSuite->prepareTests();

        $this->assertEquals($config, $testSuite->getConfiguration());
        $this->assertEquals($results, $testSuite->getTestResults());
        $this->assertEquals([
            ['state' => State::NOSTATE, 'category' => 'a', 'subject' => 'b', 'message' => ''],
            ['state' => State::NOSTATE, 'category' => 'c', 'subject' => 'd', 'message' => ''],
            ['state' => State::NOSTATE, 'category' => 'e', 'subject' => 'f', 'message' => ''],
        ], $testSuite->getArrayizeTestResults());

        $this->assertEquals('travis', $testSuite->getCategory());
        $this->assertEquals('travis', $testSuite->getSubject());

        $this->assertEquals(State::NOSTATE, $testSuite->calculateState());
        $testSuite->resetTestResults();
        $this->assertEquals(State::NOSTATE, $testSuite->calculateState());
    }
}

class TestSuiteInstance extends TestSuiteFactory
{
    public function resetTestResults()
    {
        $this->testResults = [];
    }

    public function prepareTests()
    {
        $a = new TestResult('a', 'b');
        $b = new TestResult('c', 'd');
        $c = new TestResult('e', 'f');

        $this->addTestResults([$a, $b]);
        $this->addTestResult($c);

        return [$a, $b, $c];
    }

    public function invokeTest()
    {
        $this->setCategory('travis');
        $this->setSubject('travis');
    }
}
