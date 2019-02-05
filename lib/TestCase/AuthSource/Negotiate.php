<?php

namespace SimpleSAML\Modules\Monitor\TestCase\AuthSource;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Negotiate extends \SimpleSAML\Modules\Monitor\TestCaseFactory
{
    /** @var \KRB5NegotiateAuth */
    private $handle;

    /** @var string|null */
    private $authorization;


    /*
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData)
    {
        $this->handle = $testData->getInputItem('handle');

        $authorization = $testData->getInputItem('authorization');
        $this->authorization = (is_null($authorization) || empty($authorization)) ? null : $authorization;

        parent::initialize($testData);
    }


    /*
     * @return void
     */
    public function invokeTest()
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
