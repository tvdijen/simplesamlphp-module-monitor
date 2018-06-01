<?php

namespace SimpleSAML\Module\monitor;

use \SimpleSAML\Module\monitor\DependencyInjection as DependencyInjection;
use \SimpleSAML\Configuration as ApplicationConfiguration;
use \SimpleSAML\Metadata\MetaDataStorageSource as MetaDataStorageSource;
	

final class TestConfiguration
{
    /**
     * @var ApplicationConfiguration
     */
    private $globalConfig;

    /**
     * @var ApplicationConfiguration
     */
    private $moduleConfig;

    /**
     * @var ApplicationConfiguration
     */
    private $authSourceConfig;

    /**
     * @var array
     */
    private $metadataConfig;

    /**
     * @var array
     */
    private $availableApacheModules;

    /**
     * @var array
     */
    private $availablePhpModules;

    /**
     * @var DependencyInjection
     */
    private $serverVars;

    /**
     * @var DependencyInjection
     */
    private $requestVars;

    /**
     * @param DependencyInjection $serverVars
     * @param DependencyInjection $requestVars
     */
    public function __construct($serverVars, $requestVars, $globalConfig, $authSourceConfig, $moduleConfig)
    {
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
     * @param ApplicationConfiguration $authSourceConfig
     *
     * @return void
     */
    private function setAuthsourceConfig($authSourceConfig)
    {
        assert($authSourceConfig instanceof ApplicationConfiguration);
        $this->authSourceConfig = $authSourceConfig;
    }

    /**
     * @param ApplicationConfiguration $moduleConfig
     *
     * @return void
     */
    private function setModuleConfig($moduleConfig)
    {
        assert($moduleConfig instanceof ApplicationConfiguration);
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * #param ApplicationConfiguration $globalConfig
     *
     * @return void
     */
    private function setGlobalConfig($globalConfig)
    {
        assert($globalConfig instanceof ApplicationConfiguration);
        $this->globalConfig = $globalConfig;
    }

    /**
     * @return void
     */
    private function setMetadataConfig()
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
        assert(is_array($metadata));
        $this->metadataConfig = $metadata;
    }

    /**
     * @return array
     */
    protected function getAvailableMetadataSets()
    {
        $globalConfig = $this->getGlobalConfig();
        $sets = array();
        if ($globalConfig->getBoolean('enable.saml20-idp', false)) {
            $sets = array_merge($sets, array('saml20-idp-hosted', 'saml20-sp-remote', 'saml20-idp-remote'));
        }
        if ($globalConfig->getBoolean('enable.shib13-idp', false)) {
            $sets = array_merge($sets, array('shib13-idp-hosted', 'shib13-sp-hosted', 'shib13-sp-remote', 'shib13-idp-remote'));
        }
        if ($globalConfig->getBoolean('enable.adfs-idp', false)) {
            $sets = array_merge($sets, array('adfs-idp-hosted', 'adfs-sp-remote'));
        }
        if ($globalConfig->getBoolean('enable.wsfed-sp', false)) {
            $sets = array_merge($sets, array('wsfed-sp-hosted', 'wsfed-idp-remote'));
        }
        return $sets;
    }

    /**
     * @return void
     */
    private function setAvailableApacheModules()
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
    private function getAvailableApacheModulesCgi()
    {
        $knownLocations = array(
            '/usr/sbin/httpd',
            '/usr/sbin/apache2',
            '/opt/rh/httpd24/root/usr/sbin/httpd'
        );

        $output = null;
        foreach ($knownLocations as $location) {
            if (file_exists($location)) {
                exec("$location -t -D DUMP_MODULES", $output);
                break;
            }
        }

        if ($output === null) {
            return array(); // Cannot determine available modules
        }
        array_shift($output);

        $modules = array();
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
    private function setAvailablePhpModules()
    {
        $this->availablePhpModules = array_merge(get_loaded_extensions(), get_loaded_extensions(true));
    }

    /**
     * @return array
     */
    public function getAvailableApacheModules()
    {
        return $this->availableApacheModules;
    }

    /**
     * @return array
     */
    public function getAvailablePhpModules()
    {
        return $this->availablePhpModules;
    }

    /**
     * @return DependencyInjection
     */
    public function getServerVars()
    {
        return $this->serverVars;
    }

    /**
     * @return DependencyInjection
     */
    public function getRequestVars()
    {
        return $this->requestVars;
    }

    /**
     * @return ApplicationConfiguration
     */
    public function getGlobalConfig()
    {
        assert($this->moduleConfig instanceof ApplicationConfiguration);
        return $this->globalConfig;
    }

    /**
     * @return ApplicationConfiguration
     */
    public function getModuleConfig()
    {
        assert($this->moduleConfig instanceof ApplicationConfiguration);
        return $this->moduleConfig;
    }

    /**
     * @return ApplicationConfiguration
     */
    public function getAuthSourceConfig()
    {
        assert($this->authSourceConfig instanceof ApplicationConfiguration);
        return $this->authSourceConfig;
    }

    /**
     * @return array
     */
    public function getMetadataConfig()
    {
        assert(is_array($this->metadataConfig));
        return $this->metadataConfig;
    }
}
