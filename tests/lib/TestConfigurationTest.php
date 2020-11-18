<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\DependencyInjection;
use SimpleSAML\Module\monitor\TestConfiguration;

/**
 * Tests for TestConfiguration
 */
class TestConfigurationTest extends \SimpleSAML\TestUtils\ClearStateTestCase
{
    private const FRAMEWORK = '../../../vendor/simplesamlphp/simplesamlphp-test-framework';

    public function testTestConfiguration(): void
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
                    'file' => self::FRAMEWORK . '/metadata/xml/valid-metadata-selfsigned.xml',
                ],
            ],
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

        $this->assertEquals($serverVars, $testConf->getServerVars());
        $this->assertEquals($requestVars, $testConf->getRequestVars());

        $this->assertEquals($globalConfig, $testConf->getGlobalConfig());
        $this->assertEquals($authSourceConfig, $testConf->getAuthSourceConfig());
        $this->assertEquals($moduleConfig, $testConf->getModuleConfig());

        $metadataConfig = $testConf->getMetadataConfig();
        $this->assertArrayHasKey(
            'https://idp.example.org/saml2/idp/metadata.php',
            $metadataConfig['saml20-idp-remote']
        );

        $this->assertNotEmpty($testConf->getAvailableApacheModules());
        $this->assertNotEmpty($testConf->getAvailablePhpModules());
    }
}
