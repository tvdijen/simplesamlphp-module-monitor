<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestSuite as TestSuite;

final class Search extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /*
     * @var \SimpleSAML_Auth_LDAP|null
     */
    private $connection = null;

    /*
     * @var string|null
     */
    private $base = null;

    /*
     * @var string|null
     */
    private $username = null;

    /*
     * @var string|null
     */
    private $password = null;

    /*
     * @var array
     */
    private $attributes = array();

    /*
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $authSourceData = $testData->getInput('authSourceData');

        $base = $authSourceData['search.base'];
        $base = is_array($base) ? $base[0] : $base;
        if (($i = stripos($base, 'DC=')) > 0) {
            $base = substr($base, $i);
        }
        $this->base = $base;

        $username = $authSourceData['search.username'];
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
        $this->password = $authSourceData['search.password'];
        $this->connection = $testData->getInput('connection');

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
            $distinguishedName = $connection->searchfordn($this->base, $this->attributes, $this->username);
        } catch (\Exception $e) {
            $msg = str_replace('Library - LDAP searchfordn(): ', '', $e->getMessage());
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'LDAP Search', $subject, $msg);
            return;
        }
        if ($distinguishedName !== null) {
            $this->setState(State::OK);
            $this->addMessage(State::OK, 'LDAP Search', $subject, 'Search succesful');
        } else {
            // Search for configured search.username returned no results; Shouldn't happen!!
            $this->setState(State::WARNING);
            $this->addMessage(State::WARNING, 'LDAP Search', $subject, 'Invalid search result');
        }
    }
}
