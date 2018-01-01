<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestSuite as TestSuite;

final class Connect extends \SimpleSAML\Module\monitor\TestCaseFactory
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
    private $timeout = 30;

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
        $this->hostname = $testData->getInput('hostname');

        $authsource = $testData->getInput('authsource');
        if (isSet($authsource['port'])) {
            $this->port = $authsource['port'];
        }
        if (isSet($authsource['enable_tls'])) {
            $this->enableTls = $authsource['enable_tls'];
        }
        if (isSet($authsource['timeout'])) {
            $this->timeout = $authsource['timeout'];
        }
        if (isSet($authsource['referrals'])) {
            $this->referrals = $authsource['referrals'];
        }
        if (isSet($authsource['debug'])) {
            $this->debug = $authsource['debug'];
        }
        parent::initialize($testData);
    }

    /*
     * @return void
     */
    protected function invokeTest()
    {
        try {
            $this->connection = new \SimpleSAML_Auth_LDAP(
                $this->hostname,
                $this->enableTls,
                $this->debug,
                $this->timeout,
                $this->port,
                $this->referrals
            );
        } catch (\Exception $e) {
            $this->setState(State::FATAL);
            $msg = str_replace('Library - LDAP __construct(): ', '', $e->getMessage());
            $connectString = $this->hostname;
            if (!preg_match('/^(ldap[s]?:\/\/(.*))$/', $this->hostname, $matches)) {
                $connectString = $this->hostname . ':' . $this->port;
            }
            $this->addMessage(State::FATAL, 'Network connection', $connectString, $msg);
            return;
        }
        $testsuite = $this->getTestSuite();

        // Actually connect and pull certificates whenever possible
        if (preg_match('/^(ldaps:\/\/(.*))$/', $this->hostname, $matches)) {
            $uri = str_replace('ldaps://', 'ssl://', $this->hostname) . ':636';
            $context = stream_context_create(array("ssl" => array("capture_peer_cert" => true, "verify_peer" => true)));
        } else {
            $uri = 'tcp://' . $this->hostname . ':' . $this->port;
            $context = stream_context_create();
        }

        $testData = new TestData(
            array(
                'uri' => $uri,
                'context' => $context
            )
        );
        $test = new TestCase\Network\ConnectUri($testsuite, $testData);
        $state = $test->getState();

        if ($state === State::OK) {
            $connection = $test->getOutput('connection');
            $cert = stream_context_get_params($connection);
            if (isSet($cert['options']['ssl']['peer_certificate'])) {
                $this->addOutput(openssl_x509_parse($cert['options']['ssl']['peer_certificate']), 'certData');
            }
            $this->setState(State::OK);
            $this->addMessage(State::OK, 'Network connection', $this->hostname, 'Connection established');
        } else {
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'Network connection', $this->hostname, 'Connection failed');
        }
        $this->addOutput($this->connection, 'connection');
    }
}
