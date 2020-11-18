<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

final class Bind extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var \SimpleSAML\Module\ldap\Auth\Ldap */
    private $connection;

    /** @var string */
    private $username;

    /** @var string */
    private $password;


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->connection = $testData->getInputItem('connection');
        $authSourceData = $testData->getInputItem('authSourceData');

        $this->username = $authSourceData->getString('search.username', '<< unset >>');
        $this->password = $authSourceData->getString('search.password', '<< unset >>');

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        try {
            $bind = $this->connection->bind($this->username, $this->password);
        } catch (\Exception $error) {
            // Fallthru
        }

        $testResult = new TestResult('LDAP Bind', $this->username);
        if (isset($error)) {
            // When you feed str_replace a string, outcome will be string too, but Psalm doesn't see it that way
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
