<?php

namespace Tests\SimpleSAML\Modules\Monitor\TestFiles;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;
use \SimpleSAML\Modules\Monitor\TestSuiteFactory as TestSuiteFactory;

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
