<?php

namespace SimpleSAML\Module\Monitor\Test;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\DependencyInjection;
use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\TestSuiteFactory;
use Tests\SimpleSAML\Module\Monitor\TestFiles\TestSuiteImplementation;

/**
 * Tests for TestSuiteFactory
 */
class TestSuiteFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testTestSuiteFactory(): void
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
