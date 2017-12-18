<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestSuite_Modules extends sspmod_monitor_TestSuite
{
    private $required_apache_modules = array();
    private $required_php_modules = array();

    // Important!!  Modules-names are handled case-sensitive!!
    private $store_apache_dependencies = array();
    private $store_php_dependencies = array(
        'memcache' => 'memcached|memcache',
        'phpsession' => 'session',
        'redis' => 'redis',
        'redissentinel' => 'redis',
        'riak:Store' => 'riak',
        'sql' => 'PDO'
    );

    private $module_apache_dependencies = array(
        'negotiateext' => 'mod_auth_kerb|mod_auth_gssapi'
    );
    private $module_php_dependencies = array(
        'authfacebook' => array('curl', 'json'),
        'authYubiKey' => 'curl';
// TODO: consent only requires pdo when database backend is used.. Should probably add this to required-list when processing metadata
//        'consent' => 'PDO',
        'consentAdmin' => 'PDO',
        'ldap' => 'ldap',
        'memcacheMonitor' => 'memcached|memcache',
        'negotiate' => 'krb5',
        'radius' => 'radius',
        'riak' => 'riak',
        'sqlauth' => 'PDO'
    );

    protected function initialize()
    {
        $this->setRequiredModules();
    }

    private function addRequiredApacheModule($module) {
        if (!in_array($module, $this->required_apache_modules)) {
            $this->required_apache_modules[] = $module;
        }
    }

    private function addRequiredPhpModule($module) {
        if (!in_array($module, $this->required_php_modules)) {
            $this->required_php_modules[] = $module;
        }
    }

    private function setRequiredModules() {
        // Apache Modules
        if (\SimpleSAML\Utils\HTTP::isHTTPS()) {
            $this->addRequiredApacheModule('mod_ssl');
        }
        if (function_exists('apache_get_modules')) {
            $this->addRequiredApacheModule('mod_php|mod_php5');
        }

        // PHP modules
        $composer_file = \SimpleSAML\Utils\System::resolvePath('composer.json');
        $composer_data = file_get_contents($composer_file);
        $composer = json_decode($composer_data, true);
        $composer_required = $composer['require'];

        $required = array();
        foreach ($composer_required as $ext => $ver) {
            if (preg_match('/^ext-/', $ext)) {
                $this->addRequiredPhpModule(substr($ext, 4));
            }
        }

        // Determine extra required modules
        $monitor = $this->getMonitor();
        $global_config = $monitor->getGlobalConfig();
        $store = $global_config->getValue('store.type');
        if (array_key_exists($store, $this->store_apache_dependencies)) {
            $this->addRequiredApacheModule($this->store_apache_dependencies[$store]);
        }
        if (array_key_exists($store, $this->store_php_dependencies)) {
            $this->addRequiredPhpModule($this->store_php_dependencies[$store]);
        }

        $modules = \SimpleSAML\Module::getModules();
        foreach ($modules as $module) {
            if (\SimpleSAML\Module::isModuleEnabled($module)) {
                if (array_key_exists($module, $this->module_apache_dependencies)) {
                    $dependencies = \SimpleSAML\Utils\Arrays::Arrayize($this->module_apache_dependencies[$module]);
                    foreach ($dependencies as $dependency) {
                        $this->addRequiredApacheModule($dependency);
                    }
                }
                if (array_key_exists($module, $this->module_php_dependencies)) {
                    $dependencies = \SimpleSAML\Utils\Arrays::Arrayize($this->module_php_dependencies[$module]);
                    foreach ($dependencies as $dependency) {
                        $this->addRequiredPhpModule($dependency);
                    }
                }
            }
        }
    }

    public function getAvailableApacheModules()
    {
        $monitor = $this->getMonitor();
        return $monitor->getAvailableApacheModules();
    }

    public function getAvailablePhpModules()
    {
        $monitor = $this->getMonitor();
        return $monitor->getAvailablePhpModules();
    }

    private function getRequiredModules()
    {
        return array('Apache' => $this->required_apache_modules, 'Php' => $this->required_php_modules);
    }

    private function getModuleDependencies()
    {
        return array('Apache' => $this->module_apache_dependencies, 'Php' => $this->module_php_dependencies);
    }

    protected function invokeTestSuite()
    {
        $monitor = $this->getMonitor();
        $available_modules = array('Apache' => $monitor->getAvailableApacheModules(), 'Php' => $monitor->getAvailablePhpModules());

        $required_modules = $this->getRequiredModules();
        $module_dependencies = $this->getModuleDependencies();
        $output = array();

        // Test for the availability of required modules
        foreach ($available_modules as $category => $available) {
            if (is_null($available)) {
                $output[$category][] = array(State::SKIPPED, $category, implode(', ', $required_modules[$category]), 'Unable to verify installed modules');
            } else {
                $class = 'sspmod_monitor_TestCase_Module_' . $category;
                $dependencies = array_key_exists($category, $module_dependencies) ? $module_dependencies[$category] : array();
                $required = array_key_exists($category, $required_modules) ? $required_modules[$category] : array();

                foreach ($required as $require) {
                    $test = new $class($this, array('module' => $require));
                    $this->addTest($test);

                    $state = $test->getState();
                    if ($state !== State::OK) {
                        $missing = array();
                        while ($dependency = array_search($require, $dependencies)) {
                            if (\SimpleSAML\Module::isModuleEnabled($dependency)) {
                                $missing[] = $dependency;
                            }
                            unset($dependencies[$dependency]);
                        }

                        if (!empty($missing)) {
                            $output[$category][] = array($state, $category, $test->getModule(), 'Module not loaded; dependency for ' . implode(', ', $missing));
                        } else {
                            $output[$category][] = array($state, $category, $test->getModule(), 'Module not loaded');
                        }
                    }
                }
            }
        }

        $tests = $this->getTests();
        foreach ($available_modules as $category => $available) {
            $categories = array_fill(0, count($tests), $category);
            if (!isSet($output[$category])) {
                $modules = array_map(
                  function($test, $category) {
                    return ($test->getCategory() === $category) ? $test->getModule() : false;
                  }, $tests, $categories
                );
                $modules = array_diff($modules, array(false));
                $output[$category][] = array(State::OK, $category, implode(', ', $modules), "All required modules are loaded");
            }
            $this->addMessages($output[$category], $category);
        }

        parent::invokeTestSuite();
    }
}
