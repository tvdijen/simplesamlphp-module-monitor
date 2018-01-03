<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestSuite as TestSuite;

final class Negotiate extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var bool
     */
    private $xml = false;

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
        $this->keytab = $testData->getInput('keytab');

        $xml = $testData->getInput('xml');
        $this->xml = !is_null($xml) && ((bool)$xml === true);

        $authorization = $testData->getInput('authorization');
        $this->authorization = (is_null($authorization) || empty($authorization)) ? null : $authorization;

        parent::initialize($testData);
    }

    /*
     * @return void
     */
    protected function invokeTest()
    {
        if ($this->xml === false) {
            $auth = new \KRB5NegotiateAuth($this->keytab);
            try {
                $reply = @$auth->doAuthentication();
            } catch (\Exception $e) {
                // Fallthru
                $this->setState(State::WARNING);
                $this->addMessage(State::WARNING, 'Authentication', 'Kerberos token validation', $e->getMessage());
                return;
            }

            if (is_null($this->authorization)) {
                $this->setState(State::SKIPPED);
                $this->addMessage(State::SKIPPED, 'Authentication', 'Kerberos token validation', 'Unable to authenticate; no token provided');
            } else if ($reply) {
                $this->setState(State::OK);
                $this->addMessage(State::OK, 'Authentication', 'Kerberos token validation', 'Succesfully authenticated as ' . $auth->getAuthenticatedUser());
            } else {
                $this->setState(State::WARNING);
                $this->addMessage(State::WARNING, 'Authentication', 'Kerberos token validation', "Something went wrong");
            }
        } else {
            $this->setState(State::SKIPPED);
            $this->addMessage(State::SKIPPED, 'Authentication', 'Kerberos token validation', 'Unable to authenticate');
        }
    }
}
