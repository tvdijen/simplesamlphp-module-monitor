<?php

namespace Tests\SimpleSAML\Module\Monitor\TestFiles;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\TestSuiteFactory;

class TestSuiteImplementation extends TestSuiteFactory
{
    public function prepareTests(): array
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

    public function invokeTest(): void
    {
        $this->setCategory('travis');
        $this->setSubject('travis');
    }
}
