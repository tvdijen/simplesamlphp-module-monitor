<?php

namespace SimpleSAML\Module\monitor;

use \SimpleSAML\Module\monitor\DependencyInjection as DependencyInjection;

class Monitor
{
    /**
     * @var TestConfiguration
     */
    private $configuration;

    /**
     * @var array
     */
    private $results = array();

    /**
     * @var array
     */
    private $state = array();

    /**
     * @param DependencyInjection $serverVars
     * @param DependencyInjection $requestVars
     */
    public function __construct($serverVars, $requestVars)
    {
        $this->configuration = new TestConfiguration($serverVars, $requestVars);
    }

    /**
     * @return void
     */
    public function invokeTestSuites()
    {
        $this->invokeModuleCheck();
        $this->invokeConfigurationCheck();
        $this->invokeStoreCheck();
        $this->invokeAuthSourceCheck();
        $this->invokeMetadataCheck();
    }

    /**
     * @return TestConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return integer
     */
    public function getState()
    {
        $filtered = array_diff($this->state, array(State::SKIPPED));
        return empty($filtered) ? State::NOSTATE : min($filtered);
    }

    /**
     * @return void
     */
    private function invokeModuleCheck()
    {
        $testsuite = new TestSuite\Modules($this->configuration);
        $this->results['modules'] = $testsuite->getArrayizeTestResults();
        $this->state[] = $testsuite->calculateState();
    }

    /**
     * @return void
     */
    private function invokeConfigurationCheck()
    {
        $testsuite = new TestSuite\Configuration($this->configuration);
        $this->results['configuration'] = $testsuite->getArrayizeTestResults();
        $this->state[] = $testsuite->calculateState();
    }

    /**
     * @return void
     */
    private function invokeStoreCheck()
    {
        $testsuite = new TestSuite\Store($this->configuration);
        $this->results['store'] = $testsuite->getArrayizeTestResults();
        $this->state[] = $testsuite->calculateState();
    }

    /**
     * @return void
     */
    private function invokeAuthSourceCheck()
    {
        $testsuite = new TestSuite\AuthSources($this->configuration);
        $this->results['authsources'] = $testsuite->getArrayizeTestResults();
        $this->state[] = $testsuite->calculateState();
    }

    /**
     * @return void
     */
    private function invokeMetadataCheck()
    {
        $testsuite = new TestSuite\Metadata($this->configuration);
        $this->results['metadata'] = $testsuite->getArrayizeTestResults();
        $this->state[] = $testsuite->calculateState();
    }
}
