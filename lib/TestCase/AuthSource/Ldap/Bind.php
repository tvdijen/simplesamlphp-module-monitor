<?php

namespace SimpleSAML\Modules\Monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Bind extends \SimpleSAML\Modules\Monitor\TestCaseFactory
{
    /*
     * @var \SimpleSAML\Auth\LDAP
     */
    private $connection;

    /*
     * @var string
     */
    private $username;

    /*
     * @var string
     */

    private $password;

    /*
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->connection = $testData->getInputItem('connection');
        $authSourceData = $testData->getInputItem('authSourceData');

        $this->username = $authSourceData->getString('search.username', '<< unset >>');
        $this->password = $authSourceData->getString('search.password', '<< unset >>');

        parent::initialize($testData);
    }
   
    /*
     * @return void
     */
    public function invokeTest()
    {
        try {
            $bind = $this->connection->bind($this->username, $this->password);
        } catch (\Exception $error) {
            // Fallthru
        }

        $testResult = new TestResult('LDAP Bind', $this->username);
        if (isSet($error)) {
            $msg = str_replace('Library - LDAP bind(): ', '', $error->getMessage());
            $testResult->setState(State::FATAL);
        } elseif ($bind === true) {
            $msg = 'Bind succesful';
            $testResult->setState(State::OK);
        } else {
            $msg = 'Authentication failed';
            $testResult->setState(State::ERROR);
        }

        $testResult->setMessage($msg);
        $this->setTestResult($testResult);
    }
}
