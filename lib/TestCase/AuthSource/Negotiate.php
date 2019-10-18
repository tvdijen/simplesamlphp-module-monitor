<?php

namespace SimpleSAML\Module\Monitor\TestCase\AuthSource;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;

final class Negotiate extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var \KRB5NegotiateAuth */
    private $handle;

    /** @var string|null */
    private $authorization;


    /*
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->handle = $testData->getInputItem('handle');

        $authorization = $testData->getInputItem('authorization');
        $this->authorization = (is_null($authorization) || empty($authorization)) ? null : $authorization;

        parent::initialize($testData);
    }


    /*
     * @return void
     */
    public function invokeTest(): void
    {
        $testResult = new TestResult('Authentication', 'Kerberos token validation');

        try {
            $reply = @$this->handle->doAuthentication();
        } catch (\Exception $error) {
            // Fallthru
        }

        if (isSet($error)) {
            $testResult->setState(State::WARNING);
            $testResult->setMessage($error->getMessage());
        } else if ($reply === true) {
            $testResult->setState(State::OK);
            $testResult->setMessage('Succesfully authenticated as '.$this->handle->getAuthenticatedUser());
        } else if (is_null($this->authorization)) {
            // Either misconfiguration of the browser, or user not authenticated at a KDC
            $testResult->setState(State::SKIPPED);
            $testResult->setMessage('Unable to authenticate; no token provided');
        } else { // $reply === false
            $testResult->setState(State::WARNING);
            $testResult->setMessage("Something went wrong and we couldn't tell why");
        }

        $this->setTestResult($testResult);
    }
}
