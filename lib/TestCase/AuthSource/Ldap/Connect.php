<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestCase as TestCase;

final class Connect extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $connection = null;

    private $hostname = null;
    private $port = null;
    private $enableTls = null;
    private $timeout = null;
    private $referrals = null;
    private $debug = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->hostname = $this->getInput('hostname');

        $authsourceData = $this->getInput('authsource_data');
        $this->port = $authsourceData['port'];
        $this->enableTls = $authsourceData['enable_tls'];
        $this->timeout = isSet($authsourceData['timeout']) ? $authsourceData['timeout'] : 30;
        $this->referrals = isSet($authsourceData['referrals']) ? $authsourceData['referrals'] : true;
        $this->debug = isSet($authsourceData['debug']) ? $authsourceData['debug'] : false;
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

        $test = new TestCase\Network\ConnectUri($testsuite, array('uri' => $uri, 'context' => $context));
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
