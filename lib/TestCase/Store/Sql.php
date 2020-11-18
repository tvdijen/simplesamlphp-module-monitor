<?php

namespace SimpleSAML\Module\monitor\TestCase\Store;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

final class Sql extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var string */
    private $host = '<< unset >>';


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
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
