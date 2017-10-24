<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_Network_ConnectUri extends sspmod_monitor_TestCase
{
    private $connection = null;
    private $timeout = null;
    private $context = null;
    private $uri = null;

    protected function initialize()
    {
        $this->setUri();
        $this->setTimeout();
        $this->setContext();
    }

    private function setUri()
    {
        assert(is_string($this->getInput('uri')));
        $this->uri = $this->getInput('uri');
    }

    private function setContext()
    {
        assert(is_resource($this->getInput('context')));

        $context = $this->getInput('context');
        if (is_null($context)) {
            $this->context = stream_context_create();
        } else {
            $this->context = $context;
        }
    }

    private function setTimeout()
    {
        $timeout = $this->getInput('timeout');
        $timeout = is_null($timeout) ? (int)ini_get("default_socket_timeout") : $timeout;
        assert(is_int($timeout));
        $this->timeout = $timeout;
    }

    protected function invokeTest()
    {
        $connection = stream_socket_client($this->uri, $errno, $errstr, $this->timeout, STREAM_CLIENT_CONNECT, $this->context);
        if ($connection !== false) {
            $this->connection = $connection;

            $this->setState(State::OK);
            $this->addOutput($connection, 'connection');
        } else {
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'Network connection', $uri, $errstr . ' (' . $errno . ')');
        }
    }

    public function __destruct()
    {
        if ($this->connection) {
            fclose($this->connection);
        }
    }
}
