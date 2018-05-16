<?php

namespace SimpleSAML\Module\monitor\TestFiles;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\TestSuiteFactory as TestSuiteFactory;

class TestSuiteImplementation extends TestSuiteFactory
{
    public function prepareTests()
    {
        $a = new TestResult('a', 'b');
        $b = new TestResult('c', 'd');
        $c = new TestResult('e', 'f');

        $a->setState(State::ERROR);
        $b->setState(State::WARNING);
        $c->setState(State::OK);

        $this->addTestResults([$a, $b]);
        $this->addTestResult($c);

        return [$a, $b, $c];
    }

    public function invokeTest()
    {
        $this->setCategory('travis');
        $this->setSubject('travis');
    }
}
