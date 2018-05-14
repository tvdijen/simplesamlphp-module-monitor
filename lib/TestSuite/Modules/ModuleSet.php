<?php

namespace SimpleSAML\Module\monitor\TestSuite\Modules;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
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
        if ($state === State::OK) {
            $testResult->setMessage('All required modules are loaded');
        } elseif ($state === State::SKIPPED) {
            $testResult->setMessage('Unable to verify installed modules');
        } else {
            $testResult->setMessage('Not all required modules are loaded');
        }
        $testResult->setState($state);
        $this->setTestResult($testResult);
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
