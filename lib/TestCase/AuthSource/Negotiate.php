<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_AuthSource_Negotiate extends sspmod_monitor_TestCase
{
    private $xml = null;
    private $keytab = null;
    private $hostname = null;

    protected function initialize()
    {
        $authsource_data = $this->getInput('authsource_data');
        $this->keytab = $authsource_data['keytab'];
        $this->hostname = $this->getInput('hostname');
        $this->xml = isSet($_REQUEST['xml']);
    }

    protected function invokeTest()
    {
        $xml = $this->xml;
        if ($xml === false) {
            $auth = new KRB5NegotiateAuth($this->keytab);
            try {
                $reply = @$auth->doAuthentication();
            } catch (Exception $e) {
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
