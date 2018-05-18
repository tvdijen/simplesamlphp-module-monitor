<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\Monitor as Monitor;

// This test relies on \SimpleSAML_Configuration::setPreLoadedConfig(), which is not available until after 1.15.4
if (\SimpleSAML_Configuration::getVersion() === "master" || version_compare(\SimpleSAML_Configuration::getVersion(), '1.15.4', '>')) {

/**
 * Tests for Monitor
 */
class MonitorTest extends \SimpleSAML\Test\Utils\ClearStateTestCase
{
    public function testMonitor()
    {
        $serverVars = new DependencyInjection(['SERVER_NAME' => 'localhost']);
        $requestVars = new DependencyInjection(['output' => 'travis']);
        $globalConfig_input = [
            'enable.saml20-idp' => true,
            'enable.shib13-idp' => true,
            'enable.adfs-idp' => true,
            'enable.wsfed-sp' => true,
            'metadata.sources' => [
                [
                    'type' => 'xml',
                    'file' => 'modules/monitor/tests/files/metadata.xml',
                ],
            ],
        ];
        $authSourceConfig_input = [
            'test' => 'travis'
        ];
        $moduleConfig_input = [
            'test' => 'travis'
        ];
        $globalConfig = \SimpleSAML_Configuration::loadFromArray($globalConfig_input);
        $authSourceConfig = \SimpleSAML_Configuration::loadFromArray($authSourceConfig_input);
        $moduleConfig = \SimpleSAML_Configuration::loadFromArray($moduleConfig_input);

        \SimpleSAML_Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        \SimpleSAML_Configuration::setPreLoadedConfig($moduleConfig, 'module_monitor.php');
        \SimpleSAML_Configuration::setPreLoadedConfig($authSourceConfig, 'authsources.php');

        $testConf = new TestConfiguration($serverVars, $requestVars, $globalConfig, $authSourceConfig, $moduleConfig);
        $monitor = new Monitor($testConf);
        $this->assertEquals($testConf, $monitor->getTestConfiguration());

        $monitor->invokeTestSuites();
    }
}

}
