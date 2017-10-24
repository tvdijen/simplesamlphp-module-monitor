<?php

use sspmod_monitor_State as State;

class sspmod_monitor_TestCase_Module extends sspmod_monitor_TestCase
{
    private $available = null;
    private $module = null;

    protected function initialize()
    {
        $this->setModule($this->getInput('module'));
    }

    protected function invokeTest()
    {
        $loaded = State::ERROR;
        $available = $this->getAvailable();
        $module = $this->getModule();
        foreach (explode('|', $module) as $mod) {
            if (in_array($mod, $available)) {
                $loaded = State::OK;
                $this->setModule($mod);
                break 1;
            }
        }

        $this->setState($loaded);
    }

    protected function setAvailable($available)
    {
        assert(is_array($available));
        $this->available = $available;
    }

    protected function getAvailable()
    {
        assert(is_array($this->available));
        return $this->available;
    }

    protected function setModule($module)
    {
        assert(is_string($module));
        $this->module = $module;
    }

    public function getModule()
    {
        assert(is_string($this->module));
        return $this->module;
    }
}
