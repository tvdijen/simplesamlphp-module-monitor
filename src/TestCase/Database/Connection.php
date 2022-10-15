<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\TestCase\Database;

use Exception;
use SimpleSAML\Database;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

final class Connection extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var \SimpleSAML\Database */
    private Database $db;

    /** @var string */
    private string $dsn;


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
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
            $this->db = Database::getInstance();
        } catch (Exception $error) {
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
