<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\Test;

use PHPUnit\Framework\Attributes\RequiresOperatingSystem;
use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase\Store\Sql;
use SimpleSAML\Module\monitor\TestData;

use function unlink;

/**
 * Tests for Sql
 */
final class TestSqlTest extends \SimpleSAML\TestUtils\ClearStateTestCase
{
    #[RequiresOperatingSystem('Linux')]
    public function testSqlSuccess(): void
    {
        $globalConfig_input = [
            'store.type' => 'sql',
            'store.sql.dsn' => 'sqlite:/tmp/test.sqlite',
        ];

        $globalConfig = Configuration::loadFromArray($globalConfig_input);
        Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        $testData = new TestData(['host' => 'test.localhost']);

        $test = new Sql($testData);
        $testResult = $test->getTestResult();
        $this->assertEquals(State::OK, $testResult->getState());
        unlink('/tmp/test.sqlite');
    }

    #[RequiresOperatingSystem('Linux')]
    public function testSqlFailure(): void
    {
        $globalConfig_input = [
            'store.type' => 'sql',
            'store.sql.dsn' => 'somenonexistingfile',
        ];

        $globalConfig = Configuration::loadFromArray($globalConfig_input);
        Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        $testData = new TestData(['host' => 'test.localhost']);

        $test = new Sql($testData);
        $testResult = $test->getTestResult();
        $this->assertEquals(State::FATAL, $testResult->getState());
    }
}
