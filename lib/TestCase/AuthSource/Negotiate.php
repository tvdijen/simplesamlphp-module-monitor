<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class Negotiate extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var string|null
     */
    private $keytab = null;

    /**
     * @var string|null
     */
    private $authorization = null;

    /*
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->keytab = $testData->getInputItem('keytab');

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

        $auth = new \KRB5NegotiateAuth($this->keytab);

        try {
            $reply = @$auth->doAuthentication();
        } catch (\Exception $error) {
            // Fallthru
        }

        if (isSet($error)) {
            $testResult->setState(State::WARNING);
            $testResult->setMessage($error->getMessage());
        } else if ($reply === true) {
            $testResult->setState(State::OK);
            $testResult->setMessage('Succesfully authenticated as '.$auth->getAuthenticatedUser());
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
