<?php

declare(strict_types=1);

namespace Tests\SimpleSAML\Module\monitor\TestFiles;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Module\monitor\TestSuiteFactory;

final class TestSuiteImplementation extends TestSuiteFactory
{
    /**
     * @return array<mixed>
     */
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
