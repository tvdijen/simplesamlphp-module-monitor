<?php

namespace SimpleSAML\Module\monitor;

abstract class TestCaseFactory implements TestInterface
{
    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var TestData
     */
    private $testData;

    /**
     * @var TestConfiguration
     * @deprecated
     */
    private $configuration;

    /**
     * @var TestResult
     */
    private $testResult;

    /**
     * @param TestData $testData
     */
    public function __construct($testData)
    {
        assert($testData instanceof TestData);

        $this->initialize($testData);
        $this->invokeTest();
    }

    /**
     * @param Testdata $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->setTestData($testData);
    }

    /**
     * @param string $category
     *
     * @return void
     */
    protected function setCategory($category)
    {
        assert(is_string($category));
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        assert(is_string($this->category));
        return $this->category;
    }

    /**
     * @param TestConfiguration|null $configuration
     *
     * @return void
     */
    protected function setConfiguration($configuration = null)
    {
        assert($configuration instanceof TestConfiguration);
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
     * @return TestData|null
     */
    public function getTestData()
    {
        assert($this->testData instanceof TestData || is_null($this->testData));
        return $this->testData;
    }

    /**
     * @param TestData|null $testData
     *
     * @return void
     */
    protected function setTestData($testData = null)
    {
        assert($testData instanceof TestData || is_null($testData));
        if (!is_null($testData)) {
            $this->testData = $testData;
        }
    }

    /**
     * @param TestResult $testResult
     *
     * @return void
     */
    protected function setTestResult($testResult)
    {
        assert($testResult instanceof TestResult);
        $this->testResult = $testResult;
    }

    /**
     * @return TestResult
     */
    public function getTestResult()
    {
        assert($this->testResult instanceof TestResult);
        return $this->testResult;
    }

    /**
     * @param string $subject
     *
     * @return void
     */
    protected function setSubject($subject)
    {
        assert(is_string($subject));
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        assert(is_string($this->subject));
        return $this->subject;
    }

    abstract public function invokeTest();
}
