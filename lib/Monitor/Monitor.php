<?php

namespace SimpleSAML\Module\monitor;

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
     * @param TestConfiguration $testConfiguration
     */
    public function __construct($testConfiguration)
    {
        $this->setTestConfiguration($testConfiguration);
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
    public function getTestConfiguration()
    {
        assert($this->configuration instanceof TestConfiguration);
        return $this->configuration;
    }

    /**
     * @param TestConfiguration $testConfiguration
     */
    private function setTestConfiguration($testConfiguration)
    {
        assert($testConfiguration instanceof TestConfiguration);
        $this->configuration = $testConfiguration;
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
        $filtered = array_diff($this->state, array(State::SKIPPED, State::NOSTATE));
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
        $testResult = $testsuite->getTestResult();
        $this->state[] = $testsuite->calculateState();
        $this->results['authsources'] = $testResult->getOutput();
    }

    /**
     * @return void
     */
    private function invokeMetadataCheck()
    {
        $testsuite = new TestSuite\Metadata($this->configuration);
        $testResult = $testsuite->getTestResult();
        $this->state[] = $testsuite->calculateState();
        $this->results['metadata'] = $testResult->getOutput();
    }
}
