<?php

namespace SimpleSAML\Module\monitor\TestCase\Network;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class ConnectUri extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @param resource|null
     */
    private $connection = null;

    /**
     * @param integer|null
     */
    private $timeout = null;

    /**
     * @param resource|null
     */
    private $context = null;

    /**
     * @param string|null
     */
    private $uri = null;

    /**
     * @var TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $uri = $testData->getInput('uri');
        $context = $testData->getInput('context');

        if (is_null($context)) {
            $context = stream_context_create();
        }

        $timeout = $testData->getInput('timeout');
        $timeout = is_null($timeout) ? (int)ini_get("default_socket_timeout") : $timeout;

        $this->setUri($uri);
        $this->setContext($context);
        $this->setTimeout($timeout);

        parent::initialize($testData);
    }

    /*
     * @param string $uri
     *
     * @return void
     */
    private function setUri($uri)
    {
        assert(is_string($uri));
        $this->uri = $uri;
    }

    /*
     * @param resource $context
     *
     * @return void
     */
    private function setContext($context)
    {
        assert(is_resource($context));
        $this->context = $context;
    }

    /*
     * @param integer $timeout
     *
     * @return void
     */
    private function setTimeout($timeout)
    {
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
