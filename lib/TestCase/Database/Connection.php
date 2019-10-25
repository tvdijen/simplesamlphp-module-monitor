<?php

namespace SimpleSAML\Module\Monitor\TestCase\Database;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;

final class Connection extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var \SimpleSAML\Database */
    private $db;

    /** @var string */
    private $dsn;


    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->dsn = $testData->getInputItem('dsn');
        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        try {
            $this->db = \SimpleSAML\Database::getInstance();
        } catch (\Exception $error) {
            // Fallthru
        }

        $testResult = new TestResult('Database connection', $this->dsn);

        if (isset($error)) {
            $testResult->setState(State::WARNING);
            $testResult->setMessage($error->getMessage());
        } elseif (isset($this->db)) {
            $testResult->setState(State::OK);
            $testResult->setMessage('Connection established');
            $testResult->addOutput($this->db, 'db');
        } else { // Shoud never happen
            $testResult->setState(State::WARNING);
            $testResult->setMessage("Something went wrong and we couldn't tell why");
        }

        $this->setTestResult($testResult);
    }
}
