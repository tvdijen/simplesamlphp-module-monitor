<?php

namespace SimpleSAML\Module\Monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Module\Monitor\DependencyInjection;
use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\Monitor;

/**
 * Tests for Monitor
 */
class MonitorTest extends \SimpleSAML\Test\Utils\ClearStateTestCase
{
    public function testMonitor(): void
    {
        $_SERVER['REQUEST_URI'] = '/';
        $serverVars = new DependencyInjection(['SERVER_NAME' => 'localhost']);
        $requestVars = new DependencyInjection(['output' => 'travis']);
        $globalConfig_input = [
            'enable.saml20-idp' => true,
            'enable.shib13-idp' => true,
            'enable.adfs-idp' => true,
            'enable.wsfed-sp' => true,
            'metadata.sources' => [
                [
                    'type' => 'flatfile',
                    'file' => 'modules/monitor/tests/files/saml20-idp-remote.php',
                ],
            ],
            'database.dsn' => 'mysql:host=localhost;dbname=saml',
        ];
        $authSourceConfig_input = [
            'test' => 'travis'
        ];
        $moduleConfig_input = [
            'test' => 'travis'
        ];
        $globalConfig = Configuration::loadFromArray($globalConfig_input);
        $authSourceConfig = Configuration::loadFromArray($authSourceConfig_input);
        $moduleConfig = Configuration::loadFromArray($moduleConfig_input);

        Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        Configuration::setPreLoadedConfig($moduleConfig, 'module_monitor.php');
        Configuration::setPreLoadedConfig($authSourceConfig, 'authsources.php');

        $testConf = new TestConfiguration($serverVars, $requestVars, $globalConfig, $authSourceConfig, $moduleConfig);
        $monitor = new Monitor($testConf);
        $this->assertEquals($testConf, $monitor->getTestConfiguration());

        $monitor->invokeTestSuites();
    }
}
