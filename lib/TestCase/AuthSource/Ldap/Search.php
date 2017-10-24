<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_AuthSource_Ldap_Search extends sspmod_monitor_TestCase
{
    private $connection = null;
    private $base = null;
    private $username = null;
    private $password = null;
    private $attributes = null;

    protected function initialize()
    {
        $authsource_data = $this->getInput('authsource_data');
        $this->connection = $this->getInput('connection');

        $base = $authsource_data['search.base'];
        $base = is_array($base) ? $base[0] : $base;
        if (($i = stripos($base, 'DC=')) > 0) {
            $base = substr($base, $i);
        }
        $this->base = $base;

        $username = $authsource_data['search.username'];
        $this->setSubject($username);
        if (strpos($username, 'DC=') > 0) {
            // We have been given a DN
            $username = ldap_explode_dn($username, 1);
            $this->username = $username[0];
            $this->attributes = array('cn');
        } else {
            // We have been given a sAMAccountName
            $this->username = $username;
            $this->attributes = array('sAMAccountName');
        }

        $this->password = $authsource_data['search.password'];
    }

    protected function invokeTest()
    {
        $connection = $this->connection;
        $subject = $this->getSubject();

        try {
            $dn = $connection->searchfordn($this->base, $this->attributes, $this->username);
        } catch (Exception $e) {
            $msg = str_replace('Library - LDAP searchfordn(): ', '', $e->getMessage());
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'LDAP Search', $subject, $msg);
            return;
        }
        if ($dn !== null) {
            $this->setState(State::OK);
            $this->addMessage(State::OK, 'LDAP Search', $subject, 'Search succesful');
        } else {
            // Search for configured search.username returned no results; Shouldn't happen!!
            $this->setState(State::WARNING);
            $this->addMessage(State::WARNING, 'LDAP Search', $subject, 'Invalid search result');
        }
    }
}
