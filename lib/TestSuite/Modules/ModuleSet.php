<?php

namespace SimpleSAML\Module\monitor\TestSuite\Modules;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use Webmozart\Assert\Assert;

final class ModuleSet extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** @var array */
    private $required;

    /** @var array */
    private $available;

    /** @var array */
    private $dependencies;

    /** @var string */
    private $type;


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     * @param \SimpleSAML\Module\monitor\TestData $testData
     */
    public function __construct(TestConfiguration $configuration, TestData $testData)
    {
        $this->setRequired($testData->getInputItem('required'));
        $this->setAvailable($testData->getInputItem('available'));
        $this->setDependencies($testData->getInputItem('dependencies'));
        $this->setType($testData->getInputItem('type'));
        $this->setCategory($this->type . ' modules');

        parent::__construct($configuration);
    }


    /**
     * @param array $required
     * @return void
     */
    private function setRequired(array $required): void
    {
        $this->required = $required;
    }


    /**
     * @param array $available
     * @return void
     */
    private function setAvailable(array $available): void
    {
        $this->available = $available;
    }


    /**
     * @param array $dependencies
     * @return void
     */
    private function setDependencies(array $dependencies): void
    {
        $this->dependencies = $dependencies;
    }


    /**
     * @param string $type
     * @return void
     */
    private function setType(string $type): void
    {
        $this->type = $type;
    }


    /**
     * @return void
     */
    public function invokeTest(): void
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
     * @param \SimpleSAML\Module\monitor\TestResult $testResult
     *
     * return void
     */
    protected function setTestResult(TestResult $testResult): void
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
     *
     * @return array
     */
    private function findMissingDependencies(string $module): array
    {
        $dependencies = $this->dependencies;
        $missing = [];
        while ($dependency = array_search($module, $dependencies)) {
            if (\SimpleSAML\Module::isModuleEnabled($dependency)) {
                $missing[] = $dependency;
            }
            unset($dependencies[$dependency]);
        }
        return $missing;
    }
}
