<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_AuthSource_Ldap_Bind extends sspmod_monitor_TestCase
{
    private $connection = null;
    private $username = null;
    private $password = null;

    protected function initialize()
    {
        $authsource_data = $this->getInput('authsource_data');

        $this->connection = $this->getInput('connection');
        $this->username = $authsource_data['search.username'];
        $this->password = $authsource_data['search.password'];
        $this->setSubject($this->username);
    }

    protected function invokeTest()
    {
        $connection = $this->connection;
        $subject = $this->getSubject();

        try {
            $connection->bind($this->username, $this->password);
        } catch (Exception $e) {
            $msg = str_replace('Library - LDAP bind(): ', '', $e->getMessage());
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'LDAP Bind', $subject, $msg);
            return;
        }

        $this->setState(State::OK);
        $this->addMessage(State::OK, 'LDAP Bind', $subject, 'Bind succesful');
    }
}
