<?php

namespace SimpleSAML\Module\monitor;

class Monitor
{
    /** @var \SimpleSAML\Module\monitor\TestConfiguration */
    private $configuration;

    /** @var array */
    private $results = [];

    /** @var array */
    private $state = [];


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $testConfiguration
     */
    public function __construct(TestConfiguration $testConfiguration)
    {
        $this->setTestConfiguration($testConfiguration);
    }


    /**
     * @return void
     */
    public function invokeTestSuites(): void
    {
        $this->invokeModuleCheck();
        $this->invokeConfigurationCheck();
        $this->invokeStoreCheck();
        $this->invokeAuthSourceCheck();
        $this->invokeMetadataCheck();
    }


    /**
     * @return \SimpleSAML\Module\monitor\TestConfiguration
     */
    public function getTestConfiguration(): TestConfiguration
    {
        return $this->configuration;
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $testConfiguration
     * @return void
     */
    private function setTestConfiguration(TestConfiguration $testConfiguration): void
    {
        $this->configuration = $testConfiguration;
    }


    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }


    /**
     * @return integer
     */
    public function getState(): int
    {
        $filtered = array_diff($this->state, [State::SKIPPED, State::NOSTATE]);
        return empty($filtered) ? State::NOSTATE : min($filtered);
    }


    /**
     * @return void
     */
    private function invokeModuleCheck(): void
    {
        $testsuite = new TestSuite\Modules($this->configuration);
        $this->results['modules'] = $testsuite->getArrayizeTestResults();
        $this->state[] = $testsuite->calculateState();
    }


    /**
     * @return void
     */
    private function invokeConfigurationCheck(): void
    {
        $testsuite = new TestSuite\Configuration($this->configuration);
        $this->results['configuration'] = $testsuite->getArrayizeTestResults();
        $this->state[] = $testsuite->calculateState();
    }


    /**
     * @return void
     */
    private function invokeStoreCheck(): void
    {
        $testsuite = new TestSuite\Store($this->configuration);
        $this->results['store'] = $testsuite->getArrayizeTestResults();
        $this->state[] = $testsuite->calculateState();
    }


    /**
     * @return void
     */
    private function invokeAuthSourceCheck(): void
    {
        $testsuite = new TestSuite\AuthSources($this->configuration);
        $testResult = $testsuite->getTestResult();
        $this->state[] = $testsuite->calculateState();
        $this->results['authsources'] = $testResult->getOutput();
    }


    /**
     * @return void
     */
    private function invokeMetadataCheck(): void
    {
        $testsuite = new TestSuite\Metadata($this->configuration);
        $testResult = $testsuite->getTestResult();
        $this->state[] = $testsuite->calculateState();
        $this->results['metadata'] = $testResult->getOutput();
    }
}
