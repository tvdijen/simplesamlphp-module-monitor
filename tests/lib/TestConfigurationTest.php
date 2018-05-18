<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;

// This test relies on \SimpleSAML_Configuration::setPreLoadedConfig(), which is not available until after 1.15.4
if (\SimpleSAML_Configuration::getVersion() === "master" || version_compare(\SimpleSAML_Configuration::getVersion(), '1.15.4', '>')) {

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

        $globalConfig = \SimpleSAML_Configuration::loadFromArray($globalConfig_input);
        $authSourceConfig = \SimpleSAML_Configuration::loadFromArray($authSourceConfig_input);
        $moduleConfig = \SimpleSAML_Configuration::loadFromArray($moduleConfig_input);

        \SimpleSAML_Configuration::setPreLoadedConfig($globalConfig, 'config.php');
        \SimpleSAML_Configuration::setPreLoadedConfig($moduleConfig, 'module_monitor.php');
        \SimpleSAML_Configuration::setPreLoadedConfig($authSourceConfig, 'authsources.php');

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

}
