<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor;

use SimpleSAML\Configuration;
use SimpleSAML\Metadata\MetaDataStorageSource;
use SimpleSAML\Module\monitor\DependencyInjection;

use function array_merge;
use function array_shift;
use function function_exists;
use function ltrim;
use function preg_replace;

final class TestConfiguration
{
    /** @var \SimpleSAML\Configuration */
    private Configuration $globalConfig;

    /** @var \SimpleSAML\Configuration */
    private Configuration $moduleConfig;

    /** @var \SimpleSAML\Configuration */
    private Configuration $authSourceConfig;

    /** @var array<mixed> */
    private array $metadataConfig;

    /** @var string[] */
    private array $availableApacheModules;

    /** @var string[] */
    private array $availablePhpModules;

    /** @var \SimpleSAML\Module\monitor\DependencyInjection */
    private DependencyInjection $serverVars;

    /** @var \SimpleSAML\Module\monitor\DependencyInjection */
    private DependencyInjection $requestVars;


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
        Configuration $moduleConfig,
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
        $sets = ['saml20-idp-remote'];
        if ($globalConfig->getOptionalBoolean('enable.saml20-idp', false)) {
            $sets = array_merge($sets, ['saml20-idp-hosted', 'saml20-sp-remote']);
        }
        if ($globalConfig->getOptionalBoolean('enable.adfs-idp', false)) {
            $sets = array_merge($sets, ['adfs-idp-hosted', 'adfs-sp-remote']);
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
            '/opt/rh/httpd24/root/usr/sbin/httpd',
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
     * @return string[]
     */
    public function getAvailableApacheModules(): array
    {
        return $this->availableApacheModules;
    }


    /**
     * @return string[]
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
     * @return array<mixed>
     */
    public function getMetadataConfig(): array
    {
        return $this->metadataConfig;
    }
}
