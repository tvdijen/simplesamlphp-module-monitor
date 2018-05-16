<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestSuiteFactory as TestSuiteFactory;
use \SimpleSAML\Module\monitor\TestFiles\TestSuiteImplementation as TestSuiteImplementation;

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
            \SimpleSAML_Configuration::loadFromArray(['metadata.sources' => []]),
            \SimpleSAML_Configuration::loadFromArray([]),
            \SimpleSAML_Configuration::loadFromArray([])
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
