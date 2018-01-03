<?php

namespace SimpleSAML\Module\monitor;

abstract class TestCaseFactory extends TestFactory
{
    /**
     * @var TestSuiteFactory|null
     */
    private $testSuite = null;

    /**
     * @var string|null
     */
    private $category = null;

    /**
     * @var string|null
     */
    private $subject = null;

    /**
     * @param TestSuiteFactory|null $testSuite
     * @param TestData $testData
     */
    public function __construct($testSuite = null, $testData)
    {
        assert($testSuite instanceof TestSuiteFactory);
        assert($testData instanceof TestData);

        $this->setTestSuite($testSuite);
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
     * @param TestSuiteFactory $testSuite
     *
     * @return void
     */
    private function setTestSuite($testSuite)
    {
        assert($testSuite instanceof TestSuiteFactory);
        $this->testSuite = $testSuite;
    }


    /**
     * @return TestSuiteFactory
     */
    public function getTestSuite()
    {
        assert($this->testSuite instanceof TestSuiteFactory);
        return $this->testSuite;
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
     * @return void
     */
    abstract protected function invokeTest();
}
