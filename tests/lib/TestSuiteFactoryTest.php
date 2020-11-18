<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Module\monitor\DependencyInjection;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestSuiteFactory;
use Tests\SimpleSAML\Module\monitor\TestFiles\TestSuiteImplementation;

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
