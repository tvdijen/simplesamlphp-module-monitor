<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\TestCase\Store\Sql as Sql;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\State as State;

// This test relies on \SimpleSAML\Configuration::setPreLoadedConfig(), which is not available until after 1.15.4
if (method_exists('\SimpleSAML\Configuration', 'setPreLoadedConfig')) {

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

}
