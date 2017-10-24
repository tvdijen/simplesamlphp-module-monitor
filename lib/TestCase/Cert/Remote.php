<?php

use sspmod_monitor_State as State;

// We're cheating here, because this TestCase doesn't decent from Cert, like Data and File do.
final class sspmod_monitor_TestCase_Cert_Remote extends sspmod_monitor_TestCase
{
    private $connect_string = null;
    private $context = null;

    protected function initialize()
    {
        $hostname = $this->getInput('hostname');
        $port = $this->getInput('port');
        $this->setCategory($this->getInput('category'));
        $this->connect_string = 'ssl://' . $hostname . ':' . $port;
        $this->context = stream_context_create(array("ssl" => array("capture_peer_cert" => true, "verify_peer" => false)));
    }

    protected function invokeTest()
    {
        $testsuite = $this->getTestSuite();
        $test = new sspmod_monitor_TestCase_Network_ConnectUri(
            $testsuite,
            array(
                'uri' => $this->connect_string,
                'context' => $this->context
            )
        );
        $state = $test->getState();

        if ($state === State::OK) { // Connection OK
            $connection = $test->getOutput('connection');
            $cert = stream_context_get_params($connection);
            if (isSet($cert['options']['ssl']['peer_certificate'])) {
                $test = new sspmod_monitor_TestCase_Cert_Data(
                    $testsuite,
                    array(
                        'certData' => $cert['options']['ssl']['peer_certificate'],
                        'category' => $this->getCategory()
                    )
                );
                $this->setState($test->getState());
                $this->setMessages($test->getMessages());
            } else {
                $this->setState(State::SKIPPED);
                $this->addMessage(State::SKIPPED, $this->getCategory(), $connect_string, 'Unable to capture peer certificate');
            }
        } else {
            $this->setState(State::FATAL);
            $this->setMessages($test->getMessages());
        }

        unset($test, $connection);
    }
}
