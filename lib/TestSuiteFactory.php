<?php

namespace SimpleSAML\Module\monitor;

abstract class TestSuiteFactory extends TestCaseFactory
{
    /**
     * @var array   An associative array of name => TestResult pairs
     */
    private $testResults = [];

    /**
     * @param TestConfiguration|null $configuration
     * @param TestData|null $testData
     */
    public function __construct($configuration = null, $testData = null)
    {
        assert($configuration instanceof TestConfiguration || is_null($configuration));
        assert($testData instanceof TestData || is_null($testData));

        $this->setConfiguration($configuration);
        $this->initialize($testData);
        $this->invokeTestSuite();
    }

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->setTestData($testData);
    }

    /**
     * @param TestResult $testResult
     *
     * @return void
     */
    protected function addTestResult($testResult)
    {
        assert($testResult instanceof TestResult);
        $this->testResults[] = $testResult;
    }

    /**
     * @param array $testResults
     *
     * @return void
     */
    protected function addTestResults($testResults)
    {
        assert(is_array($testResults));
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
     * @return array
     */
    public function getArrayizeTestResults()
    {
        assert(is_array($this->testResults));
        $result = [];
        foreach ($this->testResults as $testResult) {
            $result[] = $testResult->arrayizeTestResult();
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
                if ($testState < $state) {
                    $state = $testState;
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
