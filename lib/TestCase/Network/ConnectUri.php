<?php

namespace SimpleSAML\Module\monitor\TestCase\Network;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

final class ConnectUri extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var integer */
    private $timeout;

    /** @var resource */
    private $context;

    /** @var string */
    private $uri;


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $uri = $testData->getInputItem('uri');
        $context = $testData->getInputItem('context');

        if (is_null($context)) {
            $context = stream_context_create();
        }

        $timeout = $testData->getInputItem('timeout');
        $timeout = is_null($timeout) ? (int)ini_get("default_socket_timeout") : $timeout;

        assert(is_string($uri));
        assert(is_resource($context));
        assert(is_int($timeout));

        $this->setUri($uri);
        $this->setContext($context);
        $this->setTimeout($timeout);

        parent::initialize($testData);
    }


    /**
     * @param string $uri
     *
     * @return void
     */
    private function setUri(string $uri): void
    {
        $this->uri = $uri;
    }


    /**
     * @param resource $context
     *
     * @return void
     */
    private function setContext($context): void
    {
        $this->context = $context;
    }


    /**
     * @param integer $timeout
     *
     * @return void
     */
    private function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        list($errno, $errstr) = [0, ''];
        $connection = @stream_socket_client(
            $this->uri,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $this->context
        );

        $testResult = new TestResult('Network connection', $this->uri);

        if (is_resource($connection)) {
            $params = stream_context_get_params($connection);

            $testResult->addOutput($connection, 'connection');
            if (isset($params['options']['ssl']['peer_certificate'])) {
                $certData = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                $testResult->addOutput($certData, 'certData');
            }
            $testResult->setState(State::OK);
            $testResult->setMessage('Connection established');
        } else {
            $testResult->setState(State::ERROR);
            $testResult->setMessage($errstr . ' (' . $errno . ')');
        }

        $this->setTestResult($testResult);
    }
}
