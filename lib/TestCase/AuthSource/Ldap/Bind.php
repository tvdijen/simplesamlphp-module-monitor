<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Module\monitor\State as State;

final class Bind extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $connection = null;
    private $username = null;
    private $password = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $authsourceData = $this->getInput('authsource_data');

        $this->connection = $this->getInput('connection');
        $this->username = $authsourceData['search.username'];
        $this->password = $authsourceData['search.password'];
        $this->setSubject($this->username);
    }

    /*
     * @return void
     */
    protected function invokeTest()
    {
        $connection = $this->connection;
        $subject = $this->getSubject();

        try {
            $connection->bind($this->username, $this->password);
        } catch (\Exception $e) {
            $msg = str_replace('Library - LDAP bind(): ', '', $e->getMessage());
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'LDAP Bind', $subject, $msg);
            return;
        }

        $this->setState(State::OK);
        $this->addMessage(State::OK, 'LDAP Bind', $subject, 'Bind succesful');
    }
}
