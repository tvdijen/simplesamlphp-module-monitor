<?php

namespace SimpleSAML\Module\monitor\TestSuite\Modules;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestCaseFactory as TestCaseFactory;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class ModuleSet extends \SimpleSAML\Module\monitor\TestSuiteFactory
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
     * @var TestCaseFactory
     */
    private $testCase;

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->setRequired($testData->getInput('required'));
        $this->setAvailable($testData->getInput('available'));
        $this->setDependencies($testData->getInput('dependencies'));
        $this->setType($testData->getInput('type'));
        $this->setTestCase($testData->getInput('testClass'));
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
     * @param TestCase
     */
    private function setTestCase($testCase)
    {
        assert($testCase instanceof TestCaseFactory);
        $this->testCase = $testCase;
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
                    'available' => $this->available
                ]);

                $moduleTest = new $this->testCase($this, $testData);
                $moduleTestResult = $moduleTest->getTestResult();
                if ($moduleTestResult->getState() !== State::OK) {
                    $missing = array();
                    $dependencies = $this->dependencies;
                    while ($dependency = array_search($this->required, $dependencies)) {
                        if (\SimpleSAML\Module::isModuleEnabled($dependency)) {
                            $missing[] = $dependency;
                        }
                        unset($dependencies[$dependency]);
                    }
                    if (!empty($missing)) {
                        $moduleTestResult->setSubject($moduleTest->getModuleName());
                        $moduleTestResult->setMessage('Module not loaded; dependency for ' . implode(', ', $missing));
                    }
                    $this->addTestResult($moduleTestResult);
                }
            }
             $state = $this->calculateState();
        }

        $testResult = new TestResult($this->type, implode(', ', $this->required));       
        if ($state === State::OK) {
            $testResult->setMessage('All required modules are loaded');
        } elseif ($state === State::SKIPPED) {
            $testResult->setMessage('Unable to verify installed modules');
        } else {
            $testResult->setMessage('Not all required modules are loaded');
        }
        $this->setTestResult($testResult);
    }
}
