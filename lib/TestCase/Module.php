<?php

namespace SimpleSAML\Module\monitor\TestCase;

use \SimpleSAML\Module\monitor\State as State;

class Module extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $available = null;
    private $module = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->setModule($this->getInput('module'));
    }

    /*
     * @return void
     */
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

    /*
     * @return void
     */
    protected function setAvailable($available)
    {
        assert(is_array($available));
        $this->available = $available;
    }

    /*
     * @return array|null
     */
    protected function getAvailable()
    {
        assert(is_array($this->available));
        return $this->available;
    }

    /*
     * @return void
     */
    protected function setModule($module)
    {
        assert(is_string($module));
        $this->module = $module;
    }

    /*
     * @return string
     */
    public function getModule()
    {
        assert(is_string($this->module));
        return $this->module;
    }
}
