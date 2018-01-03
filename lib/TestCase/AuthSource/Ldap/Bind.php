<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestSuite as TestSuite;

final class Bind extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /*
     * @var \SimpleSAML_Auth_LDAP|null
     */
    private $connection = null;

    /*
     * @var string|null
     */
    private $username = null;

    /*
     * @var string|null
     */

    private $password = null;

    /*
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->connection = $testData->getInput('connection');
        $authSourceData = $testData->getInput('authSourceData');

        $this->username = $authSourceData['search.username'];
        $this->password = $authSourceData['search.password'];

        $this->setSubject($this->username);

        parent::initialize($testData);
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
