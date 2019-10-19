<?php

namespace SimpleSAML\Module\Monitor\TestCase\AuthSource\Ldap;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;

final class Bind extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var \SimpleSAML\Auth\LDAP */
    private $connection;

    /** @var string */
    private $username;

    /** @var string */
    private $password;


    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
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
        if (isSet($error)) {
            // When you feed str_replace a string, outcome will be string too, but Psalm doesn't see it that way

            /** @var string $msg */
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
