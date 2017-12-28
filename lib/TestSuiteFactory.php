<?php

namespace SimpleSAML\Module\monitor;

abstract class TestSuiteFactory extends TestFactory
{
    private $monitor = null;
    private $tests = null;

    /**
     * @param Monitor $monitor
     */
    public function __construct($monitor, $input)
    {
        assert($monitor instanceof Monitor);
        assert(is_array($input));

        $this->setMonitor($monitor);
        $this->setInput($input);
        $this->initialize();
        $this->setInput(null);
        $this->invokeTestSuite();
    }

    /*
     * @return void
     */
    abstract protected function initialize();

    /*
     * @param Monitor $monitor
     *
     * @return void
     */
    private function setMonitor($monitor)
    {
        assert($monitor instanceof Monitor);
        $this->monitor = $monitor;
    }

    /*
     * @return Monitor
     */
    public function getMonitor()
    {
        assert(is_a($this->monitor, 'sspmod_monitor_Monitor'));
        return $this->monitor;
    }

    /*
     * @return void
     */
    protected function addTest($test)
    {
        assert(is_a($test, 'sspmod_monitor_Test'));
        $this->tests[] = $test;
    }

    /*
     * @return array
     */
    public function getTests()
    {
        assert(is_array($this->tests));
        return $this->tests;
    }

    /*
     * @return void
     */
    protected function calculateState()
    {
        $tests = $this->getTests();

        if (!empty($tests)) {
            $overall = array();
            foreach ($tests as $test) {
                $overall[] = $test->getState();
            }
            $this->setState(min($overall));
        }
    }

    /*
     * @return void
     */
    abstract protected function invokeTestSuite();
}
