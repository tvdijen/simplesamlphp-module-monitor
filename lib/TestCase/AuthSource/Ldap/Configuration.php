<?php

namespace SimpleSAML\Module\Monitor\TestCase\AuthSource\Ldap;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;

final class Configuration extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var \SimpleSAML\Auth\LDAP|null */
    private $connection = null;

    /** @var string */
    private $hostname = '';

    /** @var integer */
    private $port = 636;

    /** @var bool */
    private $enableTls = false;

    /** @var integer */
    private $timeout = 3;

    /** @var bool */
    private $referrals = false;

    /** @var bool */
    private $debug = false;


    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $authSourceData = $testData->getInputItem('authSourceData');
        $this->hostname = $authSourceData->getString('hostname', '<< unset >>');
        $this->port = $authSourceData->getInteger('port', 636);
        $this->enableTls = $authSourceData->getBoolean('enable_tls', false);
        $this->timeout = $authSourceData->getInteger('timeout', 3);
        $this->referrals = $authSourceData->getBoolean('referrals', false);
        $this->debug = $authSourceData->getBoolean('debug', false);

        $this->setSubject($this->hostname);

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        if (preg_match('/^(ldap[s]?:\/\/(.*))$/', $this->hostname, $matches)) {
            $connectString = $this->hostname;
        } else {
            $connectString = $this->hostname . ':' . $this->port;
        }

        $testResult = new TestResult('LDAP configuration', $connectString);

        try {
            $this->connection = new \SimpleSAML\Auth\LDAP(
                $this->hostname,
                $this->enableTls,
                $this->debug,
                $this->timeout,
                $this->port,
                $this->referrals
            );
            $state = State::OK;
        } catch (\Exception $error) {
            $state = State::FATAL;
        }

        if (isset($error)) {
            // When you feed str_replace a string, outcome will be string too, but Psalm doesn't see it that way

            /** @var string $msg */
            $msg = str_replace('Library - LDAP __construct(): ', '', $error->getMessage());
        } else {
            $msg = 'Configuration syntax OK';
            $testResult->addOutput($this->connection, 'connection');
        }

        $testResult->setState($state);
        $testResult->setMessage($msg);
        $this->setTestResult($testResult);
    }
}
