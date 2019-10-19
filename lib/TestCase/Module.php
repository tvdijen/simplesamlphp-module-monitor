<?php

namespace SimpleSAML\Module\Monitor\TestCase;

use \SimpleSAML\Module\Monitor\State as State;
use \SimpleSAML\Module\Monitor\TestData as TestData;
use \SimpleSAML\Module\Monitor\TestResult as TestResult;

class Module extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var array */
    private $parsed = [];

    /** @var array */
    private $available = [];

    /** @var string */
    private $module;


    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->module = $testData->getInputItem('required');
        $available = $testData->getInputItem('available');
        if (!is_null($available)) {
            $this->available = $available;
        }
        $this->parsed = explode('|', $this->module);

        $this->setCategory($testData->getInputItem('type'));
        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $testResult = new TestResult($this->getCategory(), $this->getModuleName());

        $state = State::ERROR;
        $available = $this->getAvailable();

        foreach ($this->parsed as $module) {
            if (in_array($module, $available)) {
                $state = State::OK;
                break 1;
            }
        }

        if ($state == State::OK) {
            $testResult->setMessage('Module loaded');
        } else {
            $testResult->setMessage('Module not loaded');
        }

        $testResult->setState($state);
        $this->setTestResult($testResult);
    }


    /**
     * @return array
     */
    private function getAvailable(): array
    {
        return $this->available;
    }


    /**
     * @return string
     */
    private function getModule(): string
    {
        return $this->module;
    }


    /**
     * @return string
     */
    public function getModuleName(): string
    {
        $available = $this->available;

        foreach ($this->parsed as $module) {
            if (in_array($module, $available)) {
                return $module;
            }
        }

        return $this->getModule();
    }
}
