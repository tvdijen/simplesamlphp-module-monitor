<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use Exception;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Module\ldap\ConnectorInterface;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

use function str_replace;

final class Bind extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var \SimpleSAML\Module\ldap\ConnectorInterface */
    private ConnectorInterface $connection;

    /** @var string */
    private string $username;

    /** @var string */
    private string $password;


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->connection = $testData->getInputItem('connection');
        $authSourceData = $testData->getInputItem('authSourceData');

        $this->username = $authSourceData->getString('search.username');
        $this->password = $authSourceData->getString('search.password');

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        try {
            $bind = $this->connection->bind($this->username, $this->password);
        } catch (Error\Error $error) {
            // Fallthru
        }

        $testResult = new TestResult('LDAP Bind', $this->username);
        if (isset($error)) {
            $msg = str_replace('Library - LDAP bind(): ', '', $error->getMessage());
            $testResult->setState(State::FATAL);
        } else {
            $msg = 'Bind succesful';
            $testResult->setState(State::OK);
        }

        $testResult->setMessage($msg);
        $this->setTestResult($testResult);
    }
}
