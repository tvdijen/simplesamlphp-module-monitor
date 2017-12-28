<?php

namespace SimpleSAML\Module\monitor;

abstract class TestCaseFactory extends TestFactory
{
    private $testsuite = null;
    private $category = null;
    private $subject = null;

    public function __construct($testsuite, $input)
    {
        assert($testsuite instanceof TestSuiteFactory);
        assert(is_array($input));
        $this->setTestSuite($testsuite);
        $this->setInput($input);
        $this->initialize();
        $this->setInput(null);
        $this->invokeTest();
    }

    /*
     * @return void
     */
    protected function initialize()
    {
    }

    /*
     * @return void
     */
    private function setTestSuite($testsuite)
    {
        assert($testsuite instanceof TestSuiteFactory);
        $this->testsuite = $testsuite;
    }


    /*
     * @return TestSuiteFactory
     */
    public function getTestSuite()
    {
        assert($this->testsuite instanceof TestSuiteFactory);
        return $this->testsuite;
    }


    /*
     * @return void
     */
    protected function setSubject($subject)
    {
        assert(is_string($subject));
        $this->subject = $subject;
    }


    /*
     * @return string
     */
    public function getSubject()
    {
        assert(is_string($this->subject));
        return $this->subject;
    }


    /*
     * @return void
     */
    protected function setCategory($category)
    {
        assert(is_string($category));
        $this->category = $category;
    }


    /*
     * @return string
     */
    public function getCategory()
    {
        assert(is_string($this->category));
        return $this->category;
    }

    
    /*
     * @return void
     */
    abstract protected function invokeTest();
}
