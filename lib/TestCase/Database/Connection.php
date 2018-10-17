<?php

namespace SimpleSAML\Module\monitor\TestCase\Database;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class Connection extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var \SimpleSAML\Database
     */
    private $db = null;

    /**
     * @var string
     */
    private $dsn;

    /*
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->dsn = $testData->getInputItem('dsn');
        parent::initialize($testData);
    }

    /*
     * @return void
     */
    public function invokeTest()
    {
        try {
            $this->db = \SimpleSAML\Database::getInstance();
        } catch (\Exception $error) {
            // Fallthru
        }

        $testResult = new TestResult('Database connection', $this->dsn);

        if (isSet($error)) {
            $testResult->setState(State::WARNING);
            $testResult->setMessage($error->getMessage());
        } else if (!is_null($this->db)) {
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
