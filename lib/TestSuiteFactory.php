<?php

namespace SimpleSAML\Module\monitor;

abstract class TestSuiteFactory extends TestCaseFactory
{
    /** @var \SimpleSAML\Module\monitor\TestConfiguration */
    private $configuration;

    /** @var array An associative array of name => TestResult pairs */
    private $testResults = [];


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     * @param \SimpleSAML\Module\monitor\TestData|null $testData
     */
    public function __construct(TestConfiguration $configuration, TestData $testData = null)
    {
        $this->setConfiguration($configuration);

        parent::__construct($testData);
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestData|null $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData = null): void
    {
        if (!is_null($testData)) {
            parent::initialize($testData);
        }
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     *
     * @return void
     */
    protected function setConfiguration(TestConfiguration $configuration): void
    {
        $this->configuration = $configuration;
    }


    /**
     * @return \SimpleSAML\Module\monitor\TestConfiguration
     */
    public function getConfiguration(): TestConfiguration
    {
        return $this->configuration;
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestResult $testResult
     *
     * @return void
     */
    protected function addTestResult(TestResult $testResult): void
    {
        $this->testResults[] = $testResult;
    }


    /**
     * @param array $testResults
     *
     * @return void
     */
    protected function addTestResults(array $testResults): void
    {
        $this->testResults = array_merge($this->testResults, $testResults);
    }


    /**
     * @return array
     */
    public function getTestResults(): array
    {
        return $this->testResults;
    }


    /**
     * @param bool $includeOutput
     *
     * @return array
     */
    public function getArrayizeTestResults(bool $includeOutput = false): array
    {
        $result = [];
        foreach ($this->testResults as $testResult) {
            $result[] = $testResult->arrayizeTestResult($includeOutput);
        }
        return $result;
    }


    /**
     * @return int
     */
    public function calculateState(): int
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
}
