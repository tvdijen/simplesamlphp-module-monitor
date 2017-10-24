<?php

abstract class sspmod_monitor_TestCase extends sspmod_monitor_Test
{
    private $testsuite = null;
    private $category = null;
    private $subject = null;

    public function __construct($testsuite, $input)
    {
        assert(is_a($testsuite, 'sspmod_monitor_TestSuite'));
        assert(is_array($input));
        $this->setTestSuite($testsuite);
        $this->setInput($input);
        is_callable(array($this, 'initialize')) && $this->initialize();
        $this->setInput(null);
        $this->invokeTest();
    }

    private function setTestSuite($testsuite)
    {
        assert(is_a($testsuite, 'sspmod_monitor_TestSuite'));
        $this->testsuite = $testsuite;
    }

    public function getTestSuite()
    {
        assert(is_a($this->testsuite, 'sspmod_monitor_TestSuite'));
        return $this->testsuite;
    }

    protected function setSubject($subject)
    {
        assert(is_string($subject));
        $this->subject = $subject;
    }

    public function getSubject()
    {
        assert(is_string($this->subject));
        return $this->subject;
    }

    protected function setCategory($category)
    {
        assert(is_string($category));
        $this->category = $category;
    }

    public function getCategory()
    {
        assert(is_string($this->category));
        return $this->category;
    }

    protected abstract function invokeTest();
}
