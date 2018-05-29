<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\TestCase\Store\Sql as Sql;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\State as State;

if (method_exists('\SimpleSAML_Configuration', 'setPreLoadedConfig')) {

/**
 * Tests for Sql
 */
class TestSqlTest extends \SimpleSAML\Test\Utils\ClearStateTestCase
{
    public function testSqlSuccess()
    {
        $globalConfig_input = [
            'store.type' => 'sql',
            'store.sql.dsn' => 'sqlite:/modules/monitor/tests/files/test.sqlite',
            'store.sql.username' => 'test',
            'store.sql.password' => 'test',
            'store.sql.options' => null,
            'store.sql.prefix' => 'test'
        ];

        $globalConfig = \SimpleSAML_Configuration::loadFromArray($globalConfig_input);
        \SimpleSAML_Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        $testData = new TestData(['host' => 'test.localhost']);

        $test = new Sql($testData);
        $testResult = $test->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testSqlFailure()
    {
        $globalConfig_input = [
            'store.type' => 'sql',
            'store.sql.dsn' => '',
            'store.sql.username' => '',
            'store.sql.password' => '',
            'store.sql.options' => null,
            'store.sql.prefix' => 'test'
        ];

        $globalConfig = \SimpleSAML_Configuration::loadFromArray($globalConfig_input);
        \SimpleSAML_Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        $testData = new TestData(['host' => 'test.localhost']);

        $test = new Sql($testData);
        $testResult = $test->getTestResult();
        $this->assertEquals(State::FATAL, $testResult->getState());
    }
}

}
