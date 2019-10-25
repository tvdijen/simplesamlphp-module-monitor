<?php

namespace SimpleSAML\Module\Monitor\TestCase\Store;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;

final class Sql extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var string */
    private $host = '<< unset >>';


    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->host = $testData->getInputItem('host');
        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $testResult = new TestResult('SQL Server Health', $this->host);

        try {
            new \SimpleSAML\Store\SQL();
            $testResult->setState(State::OK);
            $testResult->setMessage('Connection to the database succesfully established');
        } catch (\Exception $e) {
            $testResult->setState(State::FATAL);
            $testResult->setMessage('Unable to connect to the database; ' . $e->getMessage());
        }

        $this->setTestResult($testResult);
    }
}
