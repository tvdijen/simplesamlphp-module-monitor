<?php

namespace SimpleSAML\Module\monitor;

class Monitor
{
    private $configuration = null;
    private $results = null;
    private $state = array();

    public function __construct()
    {
        $this->configuration = new Configuration();
    }
    
    /*
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

    /*
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /*
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /*
     * @return integer
     */
    public function getState()
    {
        $filtered = array_diff($this->state, array(State::SKIPPED));
        return empty($filtered) ? State::NOSTATE : min($filtered);
    }

    /*
     * @return void
     */
    private function invokeModuleCheck()
    {
        $testsuite = new TestSuite\Modules($this->configuration, array());
        $this->results['modules'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }

    /*
     * @return void
     */
    private function invokeConfigurationCheck()
    {
        $testsuite = new TestSuite\Configuration($this->configuration, array());
        $this->results['configuration'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }

    /*
     * @return void
     */
    private function invokeStoreCheck()
    {
        $testsuite = new TestSuite\Store($this->configuration, array());
        $this->results['store'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }

    /*
     * @return void
     */
    private function invokeAuthSourceCheck()
    {
        $testsuite = new TestSuite\AuthSources($this->configuration, array());
        $this->results['authsources'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }

    /*
     * @return void
     */
    private function invokeMetadataCheck()
    {
        $testsuite = new TestSuite\Metadata($this->configuration, array());
        $this->results['metadata'] = $testsuite->getMessages();
        $this->state[] = $testsuite->getState();
    }
}
