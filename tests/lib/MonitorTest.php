<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\DependencyInjection;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\Monitor;

/**
 * Tests for Monitor
 */
class MonitorTest extends \SimpleSAML\TestUtils\ClearStateTestCase
{
    private const FRAMEWORK = 'vendor/simplesamlphp/simplesamlphp-test-framework';

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
                    'file' => self::FRAMEWORK . '/metadata/simplesamlphp/saml20-idp-remote_cert_selfsigned.php',
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
