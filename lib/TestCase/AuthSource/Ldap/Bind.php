<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class Bind extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /*
     * @var \SimpleSAML_Auth_LDAP|null
     */
    private $connection = null;

    /*
     * @var string|null
     */
    private $username = null;

    /*
     * @var string|null
     */

    private $password = null;


    /*
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->connection = $testData->getInput('connection');
        $authSourceData = $testData->getInput('authSourceData');

        $this->username = $authSourceData['search.username'];
        $this->password = $authSourceData['search.password'];

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
            $msg = ldap_error($this->connection).' ('.ldap_errno($this->connection).')';
            $testResult->setState(State::ERROR);
        }

        $testResult->setMessage($msg);
        $this->setTestResult($testResult);
    }
}
