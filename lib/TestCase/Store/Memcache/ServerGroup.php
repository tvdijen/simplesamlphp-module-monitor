<?php

namespace SimpleSAML\Module\monitor\TestCase\Store\Memcache;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class ServerGroup extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var array
     */
    private $results = array();

    /**
     * @var string|null
     */
    private $group = null;

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $results = $testData->getInputItem('results');
        $this->$results = \SimpleSAML\Utils\Arrays::Arrayize($results);
        $this->group = $testData->getInputItem('group');

        parent::initialize($testData);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $testResult = new TestResult('Memcache Server Group Health', 'Group ' . $this->group);

        $states = array();
        foreach ($this->results as $result) {
            $states[] = $result->getState();
        }
        $state = min($states);

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
