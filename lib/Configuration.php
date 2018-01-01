<?php

namespace SimpleSAML\Module\monitor;

use \SimpleSAML_Configuration as SspConfiguration;
use \SimpleSAML_Metadata_MetaDataStorageSource as MetaDataStorageSource;

final class Configuration
{
    private $globalConfig = null;
    private $moduleConfig = null;
    private $authsourceConfig = null;
    private $metadataConfig = null;

    private $availableApacheModules = null;
    private $availablePhpModules = null;

    public function __construct()
    {
        $this->setAuthsourceConfig();
        $this->setModuleConfig();
        $this->setGlobalConfig();
        $this->setMetadataConfig();
        $this->setAvailableApacheModules();
        $this->setAvailablePhpModules();
    }

    /*
     * @return void
     */
    private function setAuthsourceConfig()
    {
        $this->authsourceConfig = SspConfiguration::getOptionalConfig('authsources.php');
    }

    /*
     * @return void
     */
    private function setModuleConfig()
    {
        $this->moduleConfig = SspConfiguration::getOptionalConfig('module_monitor.php');
    }

    /*
     * @return void
     */
    private function setGlobalConfig()
    {
        $this->globalConfig = SspConfiguration::getInstance();
    }

    /*
     * @return void
     */
    private function setMetadataConfig()
    {
        $sets = $this->getAvailableMetadataSets();
        $sources = $this->globalConfig->getValue('metadata.sources');
        $handlers = MetaDataStorageSource::parseSources($sources);
        $metadata = array();
        if (!empty($sets)) {
            foreach ($handlers as $handler) {
                foreach ($sets as $set) {
                    $metadata[$set] = $handler->getMetadataSet($set);
                }
            }
        }
        $this->metadataConfig = $metadata;
    }

    /*
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

    /*
     * @return void
     */
    private function setAvailableApacheModules()
    {
        // Determine available Apache-modules
        if (function_exists('apache_get_modules')) {
            $this->availableApacheModules = apache_get_modules();
        } else { // CGI-mode
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
                return; // Cannot determine available modules
            }
            array_shift($output);
            $modules = array();
            foreach ($output as $module) {
                $module = ltrim($module);
                if (($res = preg_replace('/(_module \((shared|static)\))/', '', $module)) !== $module) {
                    $modules[] = 'mod_' . $res;
                } // else skip
            }
            $this->availableApacheModules = $modules;
        }
    }

    /*
     * @return void
     */
    private function setAvailablePhpModules()
    {
        $this->availablePhpModules = array_merge(get_loaded_extensions(), get_loaded_extensions(true));
    }

    /*
     * @return array
     */
    public function getAvailableApacheModules()
    {
        return $this->availableApacheModules;
    }

    /*
     * @return array
     */
    public function getAvailablePhpModules()
    {
        return $this->availablePhpModules;
    }

    /*
     * @return SspConfiguration
     */
    public function getModuleConfig()
    {
        return $this->moduleConfig;
    }

    /*
     * @return SspConfiguration
     */
    public function getGlobalConfig()
    {
        return $this->globalConfig;
    }

    /*
     * @return SspConfiguration
     */
    public function getAuthSourceConfig()
    {
        return $this->authsourceConfig;
    }

    /*
     * @return array
     */
    public function getMetadataConfig()
    {
        return $this->metadataConfig;
    }
}
