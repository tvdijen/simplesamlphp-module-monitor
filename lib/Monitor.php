<?php

use \SimpleSAML_Configuration as Configuration;
use sspmod_monitor_State as State;

class sspmod_monitor_Monitor
{
    private $module_config = null;
    private $global_config = null;
    private $authsource_config = null;
    private $metadata_config = null;

    private $available_apache_modules = null;
    private $available_php_modules = null;

    private $results = null;
    private $state = array();

    private $available_metadata_sets = null;

    // Constructor
    public function __construct()
    {
        $this->setAuthsourceConfig();
        $this->setModuleConfig();
        $this->setGlobalConfig();
        $this->setMetadataConfig();
        $this->setAvailableApacheModules();
        $this->setAvailablePhpModules();
    }

    public function invokeTestSuites()
    {
        $this->invokeModuleCheck();
        $this->invokeConfigurationCheck();
        $this->invokeStoreCheck();
        $this->invokeAuthSourceCheck();
        $this->invokeMetadataCheck();
    }

    // Setters
    private function setAuthsourceConfig()
    {
        $this->authsource_config = Configuration::getOptionalConfig('authsources.php');
    }

    private function setModuleConfig()
    {
        $this->module_config = Configuration::getOptionalConfig('module_monitor.php');
    }

    private function setGlobalConfig()
    {
        $this->global_config = Configuration::getInstance();
    }

    private function setMetadataConfig()
    {
        $sets = $this->getAvailableMetadataSets();
        $sources = $this->global_config->getValue('metadata.sources');
        $handlers = SimpleSAML_Metadata_MetaDataStorageSource::parseSources($sources);

        $metadata = array();
        if (!empty($sets)) {
            foreach ($handlers as $handler) {
                foreach ($sets as $set) {
                    $metadata[$set] = $handler->getMetadataSet($set);
                }
            }
        }
        $this->metadata_config = $metadata;
    }

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

    private function setAvailableApacheModules()
    {
        // Determine available Apache-modules
        if (function_exists('apache_get_modules')) {
            $this->available_apache_modules = apache_get_modules();
        } else { // CGI-mode
            if (file_exists('/usr/sbin/httpd')) {
                exec('/usr/sbin/httpd -t -D DUMP_MODULES', $output);
            } else if (file_exists('/usr/sbin/apache2')) {
                exec('/usr/sbin/apache2 -t -D DUMP_MODULES', $output);
            } else if (file_exists('/opt/rh/httpd24/root/usr/sbin/httpd')) {
                exec('/opt/rh/httpd24/root/usr/sbin/httpd -t -D DUMP_MODULES', $output);
            } else {
                return; // Cannot determine available modules
            }
            array_shift($output);

            $modules = array();
            foreach ($output as $module) {
                $module = ltrim($module);
                if (($res = preg_replace('/(_module \(shared\))/', '', $module)) !== $module) {
                    $modules[] = 'mod_' . $res;
                } else if (($res = preg_replace('/(_module \(static\))/', '', $module)) !== $module) {
                    $modules[] = 'mod_' . $res;
                } // else skip
            }

            $this->available_apache_modules = $modules;
        }
    }

    private function setAvailablePhpModules()
    {
        $this->available_php_modules = array_merge(get_loaded_extensions(), get_loaded_extensions(true));
    }

    // Getters
    public function getAvailableApacheModules()
    {
        return $this->available_apache_modules;
    }

    public function getAvailablePhpModules()
    {
        return $this->available_php_modules;
    }

    public function getModuleConfig()
    {
        return $this->module_config;
    }

    public function getGlobalConfig()
    {
        return $this->global_config;
    }

    public function getAuthSourceConfig()
    {
        return $this->authsource_config;
    }

    public function getMetadataConfig()
    {
        return $this->metadata_config;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getState()
    {
        $filtered = array_diff($this->state, array(State::SKIPPED));
        return empty($filtered) ? State::NOSTATE : min($filtered);
    }

    private function invokeModuleCheck()
    {
        $testsuite = new sspmod_monitor_TestSuite_Modules($this, array());
        $this->results['modules'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }

    private function invokeConfigurationCheck()
    {
        $testsuite = new sspmod_monitor_TestSuite_Configuration($this, array());
        $this->results['configuration'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }

    private function invokeStoreCheck()
    {
        $testsuite = new sspmod_monitor_TestSuite_Store($this, array());
        $this->results['store'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }

    private function invokeAuthSourceCheck()
    {
        $testsuite = new sspmod_monitor_TestSuite_AuthSources($this, array());
        $this->results['authsources'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }

    private function invokeMetadataCheck()
    {
        $testsuite = new sspmod_monitor_TestSuite_Metadata($this, array());
        $this->results['metadata'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }
}
