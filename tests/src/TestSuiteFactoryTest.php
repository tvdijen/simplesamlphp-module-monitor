<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\DependencyInjection;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestData;
use Tests\SimpleSAML\Module\monitor\TestFiles\TestSuiteImplementation;

/**
 * Tests for TestSuiteFactory
 */
final class TestSuiteFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testTestSuiteFactory(): void
    {
        $config = new TestConfiguration(
            new DependencyInjection([]),
            new DependencyInjection([]),
            Configuration::loadFromArray([
                'metadata.sources' => [],
                'enable.saml20-idp' => true,
                'enable.adfs-idp' => false,
                'enable.wsfed-sp' => false,
            ]),
            Configuration::loadFromArray([]),
            Configuration::loadFromArray([]),
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
