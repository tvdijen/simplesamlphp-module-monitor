<?php

namespace SimpleSAML\Module\monitor\TestCase\Network;

use \SimpleSAML\Module\monitor\State as State;

final class ConnectUri extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $connection = null;
    private $timeout = null;
    private $context = null;
    private $uri = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->setUri();
        $this->setTimeout();
        $this->setContext();
    }

    /*
     * @return void
     */
    private function setUri()
    {
        assert(is_string($this->getInput('uri')));
        $this->uri = $this->getInput('uri');
    }

    /*
     * @return void
     */
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

    /*
     * @return void
     */
    private function setTimeout()
    {
        $timeout = $this->getInput('timeout');
        $timeout = is_null($timeout) ? (int)ini_get("default_socket_timeout") : $timeout;
        assert(is_int($timeout));
        $this->timeout = $timeout;
    }

    /*
     * @return void
     */
    protected function invokeTest()
    {
        $connection = @stream_socket_client($this->uri, $errno, $errstr, $this->timeout, STREAM_CLIENT_CONNECT, $this->context);
        if ($connection !== false) {
            $this->connection = $connection;

            $this->setState(State::OK);
            $this->addOutput($connection, 'connection');
        } else {
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'Network connection', $this->uri, $errstr . ' (' . $errno . ')');
        }
    }

    public function __destruct()
    {
        if ($this->connection) {
            fclose($this->connection);
        }
    }
}
