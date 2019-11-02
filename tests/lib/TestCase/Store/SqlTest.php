<?php

namespace SimpleSAML\Module\Monitor\Test;

use SimpleSAML\Module\Monitor\TestCase\Store\Sql;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\State;

/**
 * Tests for Sql
 */
class TestSqlTest extends \SimpleSAML\TestUtils\ClearStateTestCase
{
    public function testSqlSuccess(): void
    {
        $globalConfig_input = [
            'store.type' => 'sql',
            'store.sql.dsn' => 'sqlite:/tmp/test.sqlite',
        ];

        $globalConfig = \SimpleSAML\Configuration::loadFromArray($globalConfig_input);
        \SimpleSAML\Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        $testData = new TestData(['host' => 'test.localhost']);

        $test = new Sql($testData);
        $testResult = $test->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());
        unlink('/tmp/test.sqlite');
    }

    public function testSqlFailure(): void
    {
        $globalConfig_input = [
            'store.type' => 'sql',
            'store.sql.dsn' => 'somenonexistingfile',
        ];

        $globalConfig = \SimpleSAML\Configuration::loadFromArray($globalConfig_input);
        \SimpleSAML\Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        $testData = new TestData(['host' => 'test.localhost']);

        $test = new Sql($testData);
        $testResult = $test->getTestResult();
        $this->assertEquals(State::FATAL, $testResult->getState());
    }
}
