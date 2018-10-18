<?php

namespace SimpleSAML\Modules\Monitor\TestSuite\Modules;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class ModuleSet extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
{
    /**
     * @var array
     */
    private $required;

    /**
     * @var array
     */
    private $available;
 
    /**
     * @var array
     */
    private $dependencies;

    /**
     * @var string
     */
    private $type;

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->setRequired($testData->getInputItem('required'));
        $this->setAvailable($testData->getInputItem('available'));
        $this->setDependencies($testData->getInputItem('dependencies'));
        $this->setType($testData->getInputItem('type'));
        $this->setCategory($this->type.' modules');
    }

    /**
     * @param array
     */
    private function setRequired($required)
    {
        assert(is_array($required));
        $this->required = $required;
    }


    /**
     * @param array
     */
    private function setAvailable($available)
    {
        assert(is_array($available));
        $this->available = $available;
    }


    /**
     * @param array
     */
    private function setDependencies($dependencies)
    {
        assert(is_array($dependencies));
        $this->dependencies = $dependencies;
    }


    /**
     * @param string
     */
    private function setType($type)
    {
        assert(is_string($type));
        $this->type = $type;
    }


    /**
     * @return void
     */
    public function invokeTest()
    {
        if (empty($this->available)) {
            $state = State::SKIPPED;
        } else {
            foreach ($this->required as $module) {
                $testData = new TestData([
                    'required' => $module,
                    'available' => $this->available,
                    'type' => $this->type,
                ]);

                $moduleTest = new TestCase\Module($testData);
                $moduleTestResult = $moduleTest->getTestResult();
                if ($moduleTestResult->getState() !== State::OK) {
                    $missing = $this->findMissingDependencies($module);
                    if (!empty($missing)) {
                        $moduleTestResult->setSubject($moduleTest->getModuleName());
                        $moduleTestResult->setMessage('Module not loaded; dependency for ' . implode(', ', $missing));
                    }
                }
                $this->addTestResult($moduleTestResult);
            }
            $state = $this->calculateState();
        }

        $testResult = new TestResult($this->type, implode(', ', $this->required));
        $testResult->setState($state);
        $this->setTestResult($testResult);
    }

    /**
     * @param TestResult $testResult
     * return void
     */
    protected function setTestResult($testResult)
    {
        $state = $testResult->getState();
        if ($state === State::OK) {
            $testResult->setMessage('All required modules are loaded');
        } elseif ($state === State::SKIPPED) {
            $testResult->setMessage('Unable to verify installed modules');
        } else {
            $testResult->setMessage('Not all required modules are loaded');
        }
        parent::setTestResult($testResult);
    }

    /**
     * @param string $module
     * @return array
     */
    private function findMissingDependencies($module)
    {
        $dependencies = $this->dependencies;
        $missing = array();
        while ($dependency = array_search($module, $dependencies)) {
            if (\SimpleSAML\Module::isModuleEnabled($dependency)) {
                $missing[] = $dependency;
            }
            unset($dependencies[$dependency]);
        }
        return $missing;
    }
}
