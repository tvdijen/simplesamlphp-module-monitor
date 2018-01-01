<?php

namespace SimpleSAML\Module\monitor\TestCase;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;

class Module extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var array
     */
    private $parsed = array();

    /**
     * @var TestData $testData
     *
     * @return void
     */
    protected function initialize($testData = null)
    {
        $module = $this->getModule();
        $this->parsed = explode('|', $module);

        parent::initialize($testData);
    }

    /**
     * @return void
     */
    protected function invokeTest()
    {
        $loaded = State::ERROR;
        $available = $this->getAvailable();

        foreach ($this->parsed as $module) {
            if (in_array($module, $available)) {
                $loaded = State::OK;
                break 1;
            }
        }

        $this->setState($loaded);
    }

    /**
     * @return array|null
     */
    private function getAvailable()
    {
        $testData = $this->getTestData();
        return $testData->getInput('available');
    }

    /**
     * @return string
     */
    private function getModule()
    {
        $testData = $this->getTestData();
        return $testData->getInput('module');
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        $available = $this->getAvailable();

        foreach ($this->parsed as $module) {
            if (in_array($module, $available)) {
                return $module;
            }
        }

        return $this->getModule();
    }
}
