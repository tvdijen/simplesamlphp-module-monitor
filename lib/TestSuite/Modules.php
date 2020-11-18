<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

class Modules extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** @var array */
    private $requiredApacheModules = [];

    // Important!!  Modules-names are handled case-sensitive!!
    /** @var array */
    private $storeApacheDependencies = [];

    /** @var array */
    private $moduleApacheDependencies = [
        'negotiateext' => 'mod_auth_kerb|mod_auth_gssapi'
    ];

    /** @var array */
    private $requiredPhpModules = [];

    /** @var array */
    private $storePhpDependencies = [
        'memcache' => 'memcached|memcache',
        'phpsession' => 'session',
        'sql' => 'PDO'
    ];

    /** @var array */
    private $modulePhpDependencies = [
        'authfacebook' => ['curl', 'json'],
        'authYubiKey' => 'curl',
// TODO: consent only requires pdo when database backend is used..
//       Should probably add this to required-list when processing metadata
//        'consent' => 'PDO',
        'consentAdmin' => 'PDO',
        'ldap' => 'ldap',
        'memcacheMonitor' => 'memcached|memcache',
        'negotiate' => 'krb5',
        'radius' => 'radius',
        'sqlauth' => 'PDO'
    ];


    /**
     * @param \SimpleSAML\Module\monitor\TestData|null $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData = null): void
    {
        $this->setRequiredApacheModules();
        $this->setRequiredPhpModules();
        $this->setRequiredSspModules();
        $this->setCategory('Modules');

        parent::initialize($testData);
    }


    /**
     * @param string $module
     *
     * @return void
     */
    private function addRequiredApacheModule(string $module): void
    {
        if (!in_array($module, $this->requiredApacheModules)) {
            $this->requiredApacheModules[] = $module;
        }
    }


    /**
     * @return void
     */
    private function setRequiredApacheModules(): void
    {
        // Apache Modules
        if (function_exists('apache_get_modules')) {
            $this->addRequiredApacheModule('mod_php|mod_php5|mod_php7');
        }
        if (\SimpleSAML\Utils\HTTP::isHTTPS()) {
            $this->addRequiredApacheModule('mod_ssl');
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
     * @param string $module
     *
     * @return void
     */
    private function addRequiredPhpModule(string $module): void
    {
        if (!in_array($module, $this->requiredPhpModules)) {
            $this->requiredPhpModules[] = $module;
        }
    }


    /**
     * @return void
     */
    private function setRequiredPhpModules(): void
    {
        // PHP modules required
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
    private function setRequiredSspModules(): void
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
     * @return array
     */
    public function getAvailableApacheModules(): array
    {
        $configuration = $this->getConfiguration();
        return $configuration->getAvailableApacheModules();
    }


    /**
     * @return array
     */
    public function getAvailablePhpModules(): array
    {
        $configuration = $this->getConfiguration();
        return $configuration->getAvailablePhpModules();
    }


    /**
     * @return array
     */
    private function getRequiredApacheModules(): array
    {
        return $this->requiredApacheModules;
    }


    /**
     * @return array
     */
    private function getRequiredPhpModules(): array
    {
        return $this->requiredPhpModules;
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $configuration = $this->getConfiguration();

        // Test Apache modules
        $testData = new TestData([
            'available' => $configuration->getAvailableApacheModules(),
            'required' => $this->getRequiredApacheModules(),
            'dependencies' => $this->moduleApacheDependencies,
            'type' => 'Apache',
        ]);
        $apacheTest = new Modules\ModuleSet($configuration, $testData);
        $apacheTestResult = $apacheTest->getTestResult();

         // Test Php modules
        $testData = new TestData([
            'available' => $configuration->getAvailablePhpModules(),
            'required' => $this->getRequiredPhpModules(),
            'dependencies' => $this->modulePhpDependencies,
            'type' => 'Php',
        ]);
        $phpTest = new Modules\ModuleSet($configuration, $testData);
        $phpTestResult = $phpTest->getTestResult();

        $this->addTestResult($apacheTestResult);
        $this->addTestResult($phpTestResult);

        $testResult = new TestResult('Modules', '');
        $testResult->setState($this->calculateState());

        $this->setTestResult($testResult);
    }
}
