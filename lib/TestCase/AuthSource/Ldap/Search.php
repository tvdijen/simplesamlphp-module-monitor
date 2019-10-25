<?php

namespace SimpleSAML\Module\Monitor\TestCase\AuthSource\Ldap;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;

final class Search extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var \SimpleSAML\Module\ldap\Auth\Ldap */
    private $connection;

    /** @var string */
    private $base;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var array */
    private $attributes = [];


    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $authSourceData = $testData->getInputItem('authSourceData');

        // Just to be on the safe side, strip off any OU's and search to whole directory
        $base = $authSourceData->getArrayizeString('search.base', '<< unset >>');
        $base = is_array($base) ? $base[0] : $base;
        if (($i = intval(stripos($base, 'DC='))) > 0) {
            $base = substr($base, $i);
        }
        $this->base = $base;

        $username = $authSourceData->getString('search.username', '<< unset >>');
        $this->setSubject($username);
        if (strpos($username, 'DC=') > 0) {
            // We have been given a DN
            $username = ldap_explode_dn($username, 1);
            $this->username = $username[0];
            $this->attributes = ['cn'];
        } else {
            // We have been given a sAMAccountName
            $this->username = $username;
            $this->attributes = ['sAMAccountName'];
        }
        $this->password = $authSourceData->getString('search.password', '<< unset >>');
        $this->connection = $testData->getInputItem('connection');

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        try {
            $this->connection->searchfordn($this->base, $this->attributes, $this->username);
        } catch (\SimpleSAML\Error\Error $error) {
            // Fallthru
        }

        $testResult = new TestResult('LDAP Search', $this->getSubject());

        if (isset($error)) {
            // When you feed str_replace a string, outcome will be string too, but Psalm doesn't see it that way

            /** @var string $msg */
            $msg = str_replace('Library - LDAP searchfordn(): ', '', $error->getMessage());
            $testResult->setState(State::ERROR);
            $testResult->setMessage($msg);
        } else {
            $testResult->setState(State::OK);
            $testResult->setMessage('Search succesful');
        }

        $this->setTestResult($testResult);
    }
}
