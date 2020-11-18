<?php

namespace SimpleSAML\Module\monitor;

abstract class TestCaseFactory implements TestInterface
{
    /** @var string */
    private $category;

    /** @var string */
    private $subject;

    /** @var \SimpleSAML\Module\monitor\TestData */
    private $testData;

    /** @var \SimpleSAML\Module\monitor\TestResult */
    private $testResult;


    /**
     * @param \SimpleSAML\Module\monitor\TestData|null $testData
     */
    public function __construct(TestData $testData = null)
    {
        if (is_null($testData)) {
            $testData = new TestData([]);
        }

        $this->initialize($testData);
        $this->invokeTest();
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->setTestData($testData);
    }


    /**
     * @param string $category
     *
     * @return void
     */
    protected function setCategory(string $category): void
    {
        $this->category = $category;
    }


    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }


    /**
     * @return \SimpleSAML\Module\Monitor\TestData
     */
    public function getTestData(): TestData
    {
        return $this->testData;
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function setTestData(TestData $testData): void
    {
        $this->testData = $testData;
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestResult $testResult
     *
     * @return void
     */
    protected function setTestResult(TestResult $testResult): void
    {
        $this->testResult = $testResult;
    }


    /**
     * @return \SimpleSAML\Module\monitor\TestResult
     */
    public function getTestResult(): TestResult
    {
        return $this->testResult;
    }


    /**
     * @param string $subject
     *
     * @return void
     */
    protected function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }


    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    abstract public function invokeTest(): void;
}
