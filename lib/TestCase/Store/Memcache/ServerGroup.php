<?php

namespace SimpleSAML\Modules\Monitor\TestCase\Store\Memcache;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class ServerGroup extends \SimpleSAML\Modules\Monitor\TestCaseFactory
{
    /**
     * @var array
     */
    private $results = [];

    /**
     * @var string|null
     */
    private $group = null;

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData)
    {
        $this->results = $testData->getInputItem('results');
        $this->group = $testData->getInputItem('group');

        parent::initialize($testData);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $testResult = new TestResult('Memcache Server Group Health', 'Group '.$this->group);

        $states = [];
        foreach ($this->results as $result) {
            $states[] = $result->getState();
        }
        $state = min($states);
        if ($state !== max($states)) {
            $state = State::WARNING;
        }
        $testResult->setState($state);

        if ($state === State::OK) {
            $testResult->setMessage('Group is healthy');
        } elseif ($state === State::WARNING) {
            $testResult->setMessage('Group is crippled');
        } else {
            $testResult->setMessage('Group is down');
        }

        $this->setTestResult($testResult);
    }
}
