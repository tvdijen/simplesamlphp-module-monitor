<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\TestCase\Store\Sql as Sql;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for Sql
 */
class TestSqlTest extends \SimpleSAML\Test\Utils\ClearStateTestCase
{
    public function testSqlSuccess()
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

    public function testSqlFailure()
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
