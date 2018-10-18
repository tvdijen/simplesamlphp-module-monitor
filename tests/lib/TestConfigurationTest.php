<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;

/**
 * Tests for TestConfiguration
 */
class TestConfigurationTest extends \SimpleSAML\Test\Utils\ClearStateTestCase
{
    public function testTestConfiguration()
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

        $globalConfig = \SimpleSAML\Configuration::loadFromArray($globalConfig_input);
        $authSourceConfig = \SimpleSAML\Configuration::loadFromArray($authSourceConfig_input);
        $moduleConfig = \SimpleSAML\Configuration::loadFromArray($moduleConfig_input);

        \SimpleSAML\Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        \SimpleSAML\Configuration::setPreLoadedConfig($moduleConfig, 'module_monitor.php');
        \SimpleSAML\Configuration::setPreLoadedConfig($authSourceConfig, 'authsources.php');

        $testConf = new TestConfiguration($serverVars, $requestVars, $globalConfig, $authSourceConfig, $moduleConfig);

        $this->assertEquals($serverVars, $testConf->getServerVars());
        $this->assertEquals($requestVars, $testConf->getRequestVars());

        $this->assertEquals($globalConfig, $testConf->getGlobalConfig());
        $this->assertEquals($authSourceConfig, $testConf->getAuthSourceConfig());
        $this->assertEquals($moduleConfig, $testConf->getModuleConfig());

        $metadataConfig = $testConf->getMetadataConfig();
        $this->assertArrayHasKey('https://engine.surfconext.nl/authentication/idp/metadata', $metadataConfig['saml20-idp-remote']);

        $this->assertNotEmpty($testConf->getAvailableApacheModules());
        $this->assertNotEmpty($testConf->getAvailablePhpModules());
    }
}
