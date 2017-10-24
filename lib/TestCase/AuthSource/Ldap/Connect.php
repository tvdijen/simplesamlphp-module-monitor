<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_AuthSource_Ldap_Connect extends sspmod_monitor_TestCase
{
    private $connection = null;

    private $hostname = null;
    private $port = null;
    private $enable_tls = null;
    private $timeout = null;
    private $referrals = null;
    private $debug = null;

    protected function initialize()
    {
        $this->hostname = $this->getInput('hostname');

        $authsource_data = $this->getInput('authsource_data');
        $this->port = $authsource_data['port'];
        $this->enable_tls = $authsource_data['enable_tls'];
        $this->timeout = $authsource_data['timeout'];
        $this->referrals = $authsource_data['referrals'];
        $this->debug = $authsource_data['debug'];
    }

    protected function invokeTest()
    {
        try {
            $this->connection = new SimpleSAML_Auth_LDAP(
                $this->hostname,
                $this->enable_tls,
                $this->debug,
                $this->timeout,
                $this->port,
                $this->referrals
            );
        } catch (Exception $e) {
            $this->setState(State::FATAL);
            $msg = str_replace('Library - LDAP __construct(): ', '', $e->getMessage());
            $connect_string = $this->hostname;
            if (!preg_match('/^(ldap[s]?:\/\/(.*))$/', $this->hostname, $matches)) {
                $connect_string = $this->hostname . ':' . $this->port;
            }
            $this->addMessage(State::FATAL, 'Network connection', $connect_string, $msg);
            return;
        }
        $testsuite = $this->getTestSuite();

        // Actually connect and pull certificates whenever possible
        if (preg_match('/^(ldaps:\/\/(.*))$/', $this->hostname, $matches)) {
            $uri = str_replace('ldaps://', 'ssl://', $this->hostname) . ':636';
            $context = stream_context_create(array("ssl" => array("capture_peer_cert" => true, "verify_peer" => false)));
        } else {
            $uri = 'tcp://' . $this->hostname . ':' . $this->port;
            $context = stream_context_create();
        }

        $test = new sspmod_monitor_TestCase_Network_ConnectUri($testsuite, array('uri' => $uri, 'context' => $context));
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
