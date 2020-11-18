<?php

namespace SimpleSAML\Module\monitor;

use SimpleSAML\Configuration;
use SimpleSAML\Metadata\MetaDataStorageSource;
use SimpleSAML\Module\Monitor\DependencyInjection;

final class TestConfiguration
{
    /** @var \SimpleSAML\Configuration */
    private $globalConfig;

    /** @var \SimpleSAML\Configuration */
    private $moduleConfig;

    /** @var \SimpleSAML\Configuration */
    private $authSourceConfig;

    /** @var array */
    private $metadataConfig;

    /** @var array */
    private $availableApacheModules;

    /** @var array */
    private $availablePhpModules;

    /** @var \SimpleSAML\Module\monitor\DependencyInjection */
    private $serverVars;

    /** @var \SimpleSAML\Module\monitor\DependencyInjection */
    private $requestVars;


    /**
     * @param \SimpleSAML\Module\monitor\DependencyInjection $serverVars
     * @param \SimpleSAML\Module\monitor\DependencyInjection $requestVars
     * @param \SimpleSAML\Configuration $globalConfig
     * @param \SimpleSAML\Configuration $authSourceConfig
     * @param \SimpleSAML\Configuration $moduleConfig
     */
    public function __construct(
        DependencyInjection $serverVars,
        DependencyInjection $requestVars,
        Configuration $globalConfig,
        Configuration $authSourceConfig,
        Configuration $moduleConfig
    ) {
        $this->serverVars = $serverVars;
        $this->requestVars = $requestVars;

        $this->setAuthsourceConfig($authSourceConfig);
        $this->setModuleConfig($moduleConfig);
        $this->setGlobalConfig($globalConfig);
        $this->setMetadataConfig();
        $this->setAvailableApacheModules();
        $this->setAvailablePhpModules();
    }


    /**
     * @param \SimpleSAML\Configuration $authSourceConfig
     *
     * @return void
     */
    private function setAuthsourceConfig(Configuration $authSourceConfig): void
    {
        $this->authSourceConfig = $authSourceConfig;
    }


    /**
     * @param \SimpleSAML\Configuration $moduleConfig
     *
     * @return void
     */
    private function setModuleConfig(Configuration $moduleConfig): void
    {
        $this->moduleConfig = $moduleConfig;
    }


    /**
     * @param \SimpleSAML\Configuration $globalConfig
     *
     * @return void
     */
    private function setGlobalConfig(Configuration $globalConfig): void
    {
        $this->globalConfig = $globalConfig;
    }


    /**
     * @return void
     */
    private function setMetadataConfig(): void
    {
        $sets = $this->getAvailableMetadataSets();
        $sources = $this->globalConfig->getValue('metadata.sources');
        $handlers = MetaDataStorageSource::parseSources($sources);
        $metadata = [];
        if (!empty($sets)) {
            foreach ($handlers as $handler) {
                foreach ($sets as $set) {
                    $metadata[$set] = $handler->getMetadataSet($set);
                }
            }
        }

        $this->metadataConfig = $metadata;
    }


    /**
     * @return array
     */
    protected function getAvailableMetadataSets(): array
    {
        $globalConfig = $this->getGlobalConfig();
        $sets = ['shib13-idp-remote', 'saml20-idp-remote'];
        if ($globalConfig->getBoolean('enable.saml20-idp', false)) {
            $sets = array_merge($sets, ['saml20-idp-hosted', 'saml20-sp-remote']);
        }
        if ($globalConfig->getBoolean('enable.shib13-idp', false)) {
            $sets = array_merge($sets, ['shib13-idp-hosted', 'shib13-sp-hosted', 'shib13-sp-remote']);
        }
        if ($globalConfig->getBoolean('enable.adfs-idp', false)) {
            $sets = array_merge($sets, ['adfs-idp-hosted', 'adfs-sp-remote']);
        }
        if ($globalConfig->getBoolean('enable.wsfed-sp', false)) {
            $sets = array_merge($sets, ['wsfed-sp-hosted', 'wsfed-idp-remote']);
        }
        return $sets;
    }


    /**
     * @return void
     */
    private function setAvailableApacheModules(): void
    {
        // Determine available Apache-modules
        if (function_exists('apache_get_modules')) {
            $this->availableApacheModules = apache_get_modules();
        } else { // CGI-mode
            $this->availableApacheModules = $this->getAvailableApacheModulesCgi();
        }
    }


    /**
     * @return array
     */
    private function getAvailableApacheModulesCgi(): array
    {
        $knownLocations = [
            '/usr/sbin/httpd',
            '/usr/sbin/apache2',
            '/opt/rh/httpd24/root/usr/sbin/httpd'
        ];

        $output = [];
        foreach ($knownLocations as $location) {
            if (file_exists($location)) {
                exec("$location -t -D DUMP_MODULES", $output);
                break;
            }
        }

        if (empty($output)) {
            return $output; // Cannot determine available modules
        }
        array_shift($output);

        $modules = [];
        foreach ($output as $module) {
            $module = ltrim($module);
            if (($res = preg_replace('/(_module \((shared|static)\))/', '', $module)) !== $module) {
                $modules[] = 'mod_' . $res;
            } // else skip
        }
        return $modules;
    }


    /**
     * @return void
     */
    private function setAvailablePhpModules(): void
    {
        $this->availablePhpModules = array_merge(get_loaded_extensions(), get_loaded_extensions(true));
    }


    /**
     * @return array
     */
    public function getAvailableApacheModules(): array
    {
        return $this->availableApacheModules;
    }


    /**
     * @return array
     */
    public function getAvailablePhpModules(): array
    {
        return $this->availablePhpModules;
    }


    /**
     * @return \SimpleSAML\Module\monitor\DependencyInjection
     */
    public function getServerVars(): DependencyInjection
    {
        return $this->serverVars;
    }


    /**
     * @return \SimpleSAML\Module\monitor\DependencyInjection
     */
    public function getRequestVars(): DependencyInjection
    {
        return $this->requestVars;
    }


    /**
     * @return \SimpleSAML\Configuration
     */
    public function getGlobalConfig(): Configuration
    {
        return $this->globalConfig;
    }


    /**
     * @return \SimpleSAML\Configuration
     */
    public function getModuleConfig(): Configuration
    {
        return $this->moduleConfig;
    }


    /**
     * @return \SimpleSAML\Configuration
     */
    public function getAuthSourceConfig(): Configuration
    {
        return $this->authSourceConfig;
    }


    /**
     * @return array
     */
    public function getMetadataConfig(): array
    {
        return $this->metadataConfig;
    }
}
