<?php

use sspmod_monitor_State as State;

class sspmod_monitor_TestSuite extends sspmod_monitor_Test
{
    private $monitor = null;
    private $tests = null;

    public function __construct($monitor, $input)
    {
        assert(is_a($monitor, 'sspmod_monitor_Monitor'));
        assert(is_array($input));

        $this->setMonitor($monitor);
        $this->setInput($input);
        is_callable(array($this, 'initialize')) && $this->initialize();
        $this->setInput(null);
        $this->invokeTestSuite();
    }

    private function setMonitor($monitor)
    {
        assert(is_a($monitor, 'sspmod_monitor_Monitor'));
        $this->monitor = $monitor;
    }

    public function getMonitor()
    {
        assert(is_a($this->monitor, 'sspmod_monitor_Monitor'));
        return $this->monitor;
    }

    protected function addTest($test)
    {
        assert(is_a($test, 'sspmod_monitor_Test'));
        $this->tests[] = $test;
    }

    public function getTests()
    {
        assert(is_array($this->tests));
        return $this->tests;
    }

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

    protected function invokeTestSuite()
    {
        $this->calculateState();
    }
}
