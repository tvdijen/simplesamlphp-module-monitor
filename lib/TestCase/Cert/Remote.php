<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

// We're cheating here, because this TestCase doesn't decent from Cert, like Data and File do.
final class Remote extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /*
     * @var string|null
     */
    private $connectString = null;

    /*
     * @var resource|null
     */
    private $context = null;

    /*
     * @param TestData $testData
     */
    protected function initialize($testData)
    {
        $hostname = $testData->getInput('hostname');
        $port = $testData->getInput('port');
        $this->setCategory($testData->getInput('category'));
        $this->connectString = 'ssl://' . $hostname . ':' . $port;
        $this->context = stream_context_create(
            array(
                "ssl" => array(
                    "capture_peer_cert" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false
                )
            )
        );

        parent::initialize($testData);
    }

    protected function invokeTest()
    {
        $testsuite = $this->getTestSuite();
        $testData = new TestData(
            array(
                'uri' => $this->connectString,
                'context' => $this->context
            )
        );
        $test = new TestCase\Network\ConnectUri(
            $testsuite,
            $testData
        );
        $state = $test->getState();

        if ($state === State::OK) { // Connection OK
            $connection = $test->getOutput('connection');
            $cert = stream_context_get_params($connection);
            if (isSet($cert['options']['ssl']['peer_certificate'])) {
                $testData = new TestData(
                    array(
                        'certData' => $cert['options']['ssl']['peer_certificate'],
                        'category' => $this->getCategory()
                    )
                );
                
                $test = new TestCase\Cert\Data(
                    $testsuite,
                    $testData
                );
                $this->setState($test->getState());
                $this->setMessages($test->getMessages());
            } else {
                $this->setState(State::SKIPPED);
                $this->addMessage(State::SKIPPED, $this->getCategory(), $this->connectString, 'Unable to capture peer certificate');
            }
        } else {
            $this->setState(State::FATAL);
            $this->setMessages($test->getMessages());
        }

        unset($test, $connection);
    }
}
