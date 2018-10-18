<?php

namespace SimpleSAML\Modules\Monitor\TestCase\Database;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Connection extends \SimpleSAML\Modules\Monitor\TestCaseFactory
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
