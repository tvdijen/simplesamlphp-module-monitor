<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource;

use \SimpleSAML\Module\monitor\State as State;

final class Negotiate extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $xml = null;
    private $keytab = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->keytab = $this->getInput('keytab');
        $this->xml = isSet($_REQUEST['xml']);
    }

    /*
     * @return void
     */
    protected function invokeTest()
    {
        $xml = $this->xml;
        if ($xml === false) {
            $auth = new \KRB5NegotiateAuth($this->keytab);
            try {
                $reply = @$auth->doAuthentication();
            } catch (\Exception $e) {
                // Fallthru
                $this->setState(State::WARNING);
                $this->addMessage(State::WARNING, 'Authentication', 'Kerberos token validation', $e->getMessage());
                return;
            }

            if (!isSet($_SERVER['HTTP_AUTHORIZATION']) || empty($_SERVER['HTTP_AUTHORIZATION'])) {
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
