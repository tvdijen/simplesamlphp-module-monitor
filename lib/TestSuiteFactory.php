<?php

namespace SimpleSAML\Modules\Monitor;

abstract class TestSuiteFactory extends TestCaseFactory
{
    /** @var TestConfiguration */
    private $configuration;

    /** @var array An associative array of name => TestResult pairs */
    private $testResults = [];


    /**
     * @param TestConfiguration|null $configuration
     * @param TestData|null $testData
     */
    public function __construct(TestConfiguration $configuration = null, TestData $testData = null)
    {
        $this->setConfiguration($configuration);
        $this->initialize($testData);
        $this->invokeTestSuite();
    }


    /**
     * @param TestData|null $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData = null)
    {
        $this->setTestData($testData);
    }


    /**
     * @param TestConfiguration $configuration
     *
     * @return void
     */
    protected function setConfiguration(TestConfiguration $configuration = null)
    {
        if (!is_null($configuration)) {
            $this->configuration = $configuration;
        }
    }


    /**
     * @return TestConfiguration
     */
    public function getConfiguration()
    {
        assert($this->configuration instanceof TestConfiguration);
        return $this->configuration;
    }


    /**
     * @param TestResult $testResult
     *
     * @return void
     */
    protected function addTestResult(TestResult $testResult)
    {
        $this->testResults[] = $testResult;
    }


    /**
     * @param array $testResults
     *
     * @return void
     */
    protected function addTestResults(array $testResults)
    {
        $this->testResults = array_merge($this->testResults, $testResults);
    }


    /**
     * @return array
     */
    public function getTestResults()
    {
        assert(is_array($this->testResults));
        return $this->testResults;
    }


    /**
     * param bool $includeOutput
     *
     * @return array
     */
    public function getArrayizeTestResults($includeOutput = false)
    {
        assert(is_array($this->testResults));
        $result = [];
        foreach ($this->testResults as $testResult) {
            $result[] = $testResult->arrayizeTestResult($includeOutput);
        }
        return $result;
    }


    /**
     * @return int
     */
    public function calculateState()
    {
        $testResults = $this->getTestResults();

        if (!empty($testResults)) {
            $state = State::OK;
            foreach ($testResults as $testResult) {
                $testState = $testResult->getState();
                if ($testState !== State::NOSTATE && $testState !== State::SKIPPED) {
                    if ($testState < $state) {
                        $state = $testState;
                    }
                }
            }
        } else {
            $state = State::NOSTATE;
        }
        return $state;
    }


    /**
     * @return void
     */
    public function invokeTestSuite()
    {
        $this->invokeTest();
    }
}
