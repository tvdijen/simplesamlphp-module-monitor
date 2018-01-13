<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class Configuration extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /*
     * @var \SimpleSAML_Auth_LDAP|null
     */
    private $connection = null;

    /*
     * @var string|null
     */
    private $hostname = null;

    /*
     * @var integer
     */
    private $port = 636;

    /*
     * @var bool
     */
    private $enableTls = true;

    /*
     * @var integer
     */
    private $timeout = 3;

    /*
     * @var bool
     */
    private $referrals = false;

    /*
     * @var bool
     */
    private $debug = false;


    /*
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $authSourceData = $testData->getInputItem('authSourceData');
        if (isSet($authSourceData['hostname'])) {
            $this->hostname = $authSourceData['hostname'];
        }
        if (isSet($authSourceData['port'])) {
            $this->port = $authSourceData['port'];
        }
        if (isSet($authSourceData['enable_tls'])) {
            $this->enableTls = $authSourceData['enable_tls'];
        }
        if (isSet($authSourceData['timeout'])) {
            $this->timeout = $authSourceData['timeout'];
        }
        if (isSet($authSourceData['referrals'])) {
            $this->referrals = $authSourceData['referrals'];
        }
        if (isSet($authSourceData['debug'])) {
            $this->debug = $authSourceData['debug'];
        }

        $this->setSubject($this->hostname);

        parent::initialize($testData);
    }


    /*
     * @return void
     */
    public function invokeTest()
    {
        if (preg_match('/^(ldap[s]?:\/\/(.*))$/', $this->hostname, $matches)) {
            $connectString = $this->hostname;
        } else {
            $connectString = $this->hostname .':'.$this->port;
        }

        $testResult = new TestResult('LDAP configuration', $connectString);

        try {
            $this->connection = new \SimpleSAML_Auth_LDAP(
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

        if (isSet($error)) {
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
