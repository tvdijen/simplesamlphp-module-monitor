<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;
use \SimpleSAML\Modules\Monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\TestSuiteFactory as TestSuiteFactory;
use \Tests\SimpleSAML\Modules\Monitor\TestFiles\TestSuiteImplementation as TestSuiteImplementation;

/**
 * Tests for TestSuiteFactory
 */
class TestSuiteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testTestSuiteFactory()
    {
        $config = new TestConfiguration(
            new DependencyInjection([]),
            new DependencyInjection([]),
            \SimpleSAML\Configuration::loadFromArray(['metadata.sources' => []]),
            \SimpleSAML\Configuration::loadFromArray([]),
            \SimpleSAML\Configuration::loadFromArray([])
        );
        $testData = new TestData(['travis' => 'travis', 'test' => 'travis']);
        $testSuite = new TestSuiteImplementation($config, $testData);
        $this->assertEquals(State::NOSTATE, $testSuite->calculateState());
        $this->assertEquals($testData, $testSuite->getTestData());

        $results = $testSuite->prepareTests();

        $this->assertEquals($config, $testSuite->getConfiguration());
        $this->assertEquals($results, $testSuite->getTestResults());
        $this->assertEquals([
            ['state' => State::ERROR, 'category' => 'a', 'subject' => 'b', 'message' => ''],
            ['state' => State::WARNING, 'category' => 'c', 'subject' => 'd', 'message' => ''],
            ['state' => State::OK, 'category' => 'e', 'subject' => 'f', 'message' => ''],
        ], $testSuite->getArrayizeTestResults());

        $this->assertEquals('travis', $testSuite->getCategory());
        $this->assertEquals('travis', $testSuite->getSubject());

        $this->assertEquals(State::ERROR, $testSuite->calculateState());
    }
}
