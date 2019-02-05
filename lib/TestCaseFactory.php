<?php

namespace SimpleSAML\Modules\Monitor;

abstract class TestCaseFactory implements TestInterface
{
    /** @var string */
    private $category;

    /** @var string */
    private $subject;

    /** @var TestData */
    private $testData;

    /** @var TestResult */
    private $testResult;


    /**
     * @param TestData|null $testData
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
     * @param Testdata $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData)
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
     * @return TestData
     */
    public function getTestData()
    {
        assert($this->testData instanceof TestData);
        return $this->testData;
    }


    /**
     * @param TestData|null $testData
     *
     * @return void
     */
    protected function setTestData(TestData $testData = null)
    {
        $this->testData = $testData;
    }


    /**
     * @param TestResult $testResult
     *
     * @return void
     */
    protected function setTestResult(TestResult $testResult)
    {
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
