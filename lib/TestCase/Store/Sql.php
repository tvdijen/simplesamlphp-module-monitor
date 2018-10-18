<?php

namespace SimpleSAML\Modules\Monitor\TestCase\Store;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Sql extends \SimpleSAML\Modules\Monitor\TestCaseFactory
{
    /**
     * @var string|null
     */
    private $host;

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->host = $testData->getInputItem('host');
        parent::initialize($testData);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $testResult = new TestResult('SQL Server Health', $this->host);

        try {
            new \SimpleSAML\Store\SQL();
            $testResult->setState(State::OK);
            $testResult->setMessage('Connection to the database succesfully established');
        } catch (\Exception $e) {
            $testResult->setState(State::FATAL);
            $testResult->setMessage('Unable to connect to the database; '.$e->getMessage());
        }

        $this->setTestResult($testResult);
    }
}
