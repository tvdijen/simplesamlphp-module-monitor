<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class Modules extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @var array
     */
    private $requiredApacheModules = array();

    /**
     * @var array
     */
    private $requiredPhpModules = array();

    /**
     * @var array
     */
    // Important!!  Modules-names are handled case-sensitive!!
    private $storeApacheDependencies = array();

    /**
     * @var array
     */
    private $storePhpDependencies = array(
        'memcache' => 'memcached|memcache',
        'phpsession' => 'session',
        'redis' => 'redis',
        'redissentinel' => 'redis',
        'riak:Store' => 'riak',
        'sql' => 'PDO'
    );

    /**
     * @var array
     */
    private $moduleApacheDependencies = array(
        'negotiateext' => 'mod_auth_kerb|mod_auth_gssapi'
    );

    /**
     * @var array
     */
    private $modulePhpDependencies = array(
        'authfacebook' => array('curl', 'json'),
        'authYubiKey' => 'curl',
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

    /**
     * @param TestConfiguration|null $configuration
     */
    public function __construct($configuration = null)
    {
        parent::__construct($configuration);
    }

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData = null)
    {
        $this->setRequiredApacheModules();
        $this->setRequiredPhpModules();
        $this->setRequiredSspModules();
    }

    /**
     * @return void
     */
    private function addRequiredApacheModule($module)
    {
        if (!in_array($module, $this->requiredApacheModules)) {
            $this->requiredApacheModules[] = $module;
        }
    }

    /**
     * @return void
     */
    private function addRequiredPhpModule($module)
    {
        if (!in_array($module, $this->requiredPhpModules)) {
            $this->requiredPhpModules[] = $module;
        }
    }

    /**
     * @return void
     */
    private function setRequiredApacheModules()
    {
        // Apache Modules
        if (\SimpleSAML\Utils\HTTP::isHTTPS()) {
            $this->addRequiredApacheModule('mod_ssl');
        }
        if (function_exists('apache_get_modules')) {
            $this->addRequiredApacheModule('mod_php|mod_php5');
        }

        // Determine extra required modules
        $configuration = $this->getConfiguration();
        $globalConfig = $configuration->getGlobalConfig();
        $store = $globalConfig->getValue('store.type');
        if (array_key_exists($store, $this->storeApacheDependencies)) {
            $this->addRequiredApacheModule($this->storeApacheDependencies[$store]);
        }
    }

    /**
     * @return void
     */
    private function setRequiredPhpModules()
    {
        // PHP modules
        $composerFile = \SimpleSAML\Utils\System::resolvePath('composer.json');
        $composerData = file_get_contents($composerFile);
        $composer = json_decode($composerData, true);
        $composerRequired = $composer['require'];

        foreach ($composerRequired as $ext => $ver) {
            if (preg_match('/^ext-/', $ext)) {
                $this->addRequiredPhpModule(substr($ext, 4));
            }
        }

        // Determine extra required modules
        $configuration = $this->getConfiguration();
        $globalConfig = $configuration->getGlobalConfig();
        $store = $globalConfig->getValue('store.type');
        if (array_key_exists($store, $this->storePhpDependencies)) {
            $this->addRequiredPhpModule($this->storePhpDependencies[$store]);
        }
    }

    /**
     * @return void
     */
    private function setRequiredSspModules()
    {
        $modules = \SimpleSAML\Module::getModules();
        foreach ($modules as $module) {
            if (\SimpleSAML\Module::isModuleEnabled($module)) {
                if (array_key_exists($module, $this->moduleApacheDependencies)) {
                    $dependencies = \SimpleSAML\Utils\Arrays::Arrayize($this->moduleApacheDependencies[$module]);
                    foreach ($dependencies as $dependency) {
                        $this->addRequiredApacheModule($dependency);
                    }
                }
                if (array_key_exists($module, $this->modulePhpDependencies)) {
                    $dependencies = \SimpleSAML\Utils\Arrays::Arrayize($this->modulePhpDependencies[$module]);
                    foreach ($dependencies as $dependency) {
                        $this->addRequiredPhpModule($dependency);
                    }
                }
            }
        }
    }

    /**
     * @return array<string,array>
     */
    public function getAvailableModules()
    {
        $configuration = $this->getConfiguration();
        return array(
            'Apache' => $configuration->getAvailableApacheModules(),
            'Php' => $configuration->getAvailablePhpModules()
        );
    }

    /**
     * @return array<string,array>
     */
    private function getRequiredModules()
    {
        return array('Apache' => $this->requiredApacheModules, 'Php' => $this->requiredPhpModules);
    }

    /**
     * @return array<string,array>
     */
    private function getModuleDependencies()
    {
        return array('Apache' => $this->moduleApacheDependencies, 'Php' => $this->modulePhpDependencies);
    }

    /**
     * @return void
     */
    protected function invokeTestSuite()
    {
        $availableModules = $this->getAvailableModules();
        $requiredModules = $this->getRequiredModules();
        $moduleDependencies = $this->getModuleDependencies();
        $output = array();

        // Test for the availability of required modules
        foreach ($availableModules as $category => $available) {
            if (is_null($available)) {
                $output[$category][] = array(State::SKIPPED, $category, implode(', ', $requiredModules[$category]), 'Unable to verify installed modules');
            } else {
                $dependencies = array_key_exists($category, $moduleDependencies) ? $moduleDependencies[$category] : array();
                $required = array_key_exists($category, $requiredModules) ? $requiredModules[$category] : array();
                $available = array_key_exists($category, $availableModules) ? $availableModules[$category] : array();

                foreach ($required as $require) {
                    $testData = new TestData(
                        array(
                            'require' => $require,
                            'available' => $available
                        )
                    );
                    $this->testRequirement($testData, $category, $dependencies, $output);
                }
            }
        }

        $this->calculateState($output);
    }

    /**
     * @param array $output
     *
     * @return void
     */
    protected function calculateState($output)
    {
        $availableModules = $this->getAvailableModules();
        $tests = $this->getTests();

        foreach ($availableModules as $category => $available) {
            $categories = array_fill(0, count($tests), $category);
            if (!isSet($output[$category])) {
                $modules = array_map(
                  function($test, $category) {
                    return ($test->getCategory() === $category) ? $test->getModuleName() : false;
                  }, $tests, $categories
                );
                $modules = array_diff($modules, array(false));
                $output[$category][] = array(State::OK, $category, implode(', ', $modules), "All required modules are loaded");
            }
            $this->addMessages($output[$category], $category);
        }

        parent::calculateState();
    }

    /**
     * @param TestData $testData
     * @param string $category
     * @param array $dependencies
     * @param array $output
     *
     * @return void
     */
    private function testRequirement($testData, $category, $dependencies, &$output)
    {
        $require = $testData->getInput('require');
        $class = '\\SimpleSAML\\Module\\monitor\\TestCase\\Module\\' . $category;

        $test = new $class($this, $testData);
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
        }

        if (!empty($missing)) {
            $output[$category][] = array($state, $category, $test->getModuleName(), 'Module not loaded; dependency for ' . implode(', ', $missing));
        } else {
            $output[$category][] = array($state, $category, $test->getModuleName(), 'Module not loaded');
        }
    }
}
