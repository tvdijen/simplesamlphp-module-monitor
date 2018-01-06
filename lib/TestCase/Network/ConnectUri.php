<?php

namespace SimpleSAML\Module\monitor\TestCase\Network;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class ConnectUri extends \SimpleSAML\Module\monitor\TestCaseFactory
{
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
    public function invokeTest()
    {
        $connection = @stream_socket_client($this->uri, $errno, $errstr, $this->timeout, STREAM_CLIENT_CONNECT, $this->context);

        $testResult = new TestResult('Network connection', $this->uri);

        if (is_resource($connection)) {
            $params = stream_context_get_params($connection);

            $testResult->addOutput($connection, 'connection');
            if (isSet($params['options']['ssl']['peer_certificate'])) {
                $certData = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                $testResult->addOutput($certData, 'certData');
            }
            $testResult->setState(State::OK);
            fclose($this->connection);
        } else {
            $testResult->setState(State::ERROR);
            $testResult->setMessage($errstr.' ('.$errno.')');
        }

        $this->setTestResult($testResult);
    }
}
