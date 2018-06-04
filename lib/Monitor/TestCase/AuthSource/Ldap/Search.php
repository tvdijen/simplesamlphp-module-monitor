<?php

namespace SimpleSAML\Module\monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class Search extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /*
     * @var \SimpleSAML\Auth\LDAP
     */
    private $connection;

    /*
     * @var string
     */
    private $base;

    /*
     * @var string
     */
    private $username;

    /*
     * @var string
     */
    private $password;

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
        $authSourceData = $testData->getInputItem('authSourceData');

        // Just to be on the safe side, strip off any OU's and search to whole directory
        $base = $authSourceData->getString('search.base', '<< unset >>');
        $base = is_array($base) ? $base[0] : $base;
        if (($i = stripos($base, 'DC=')) > 0) {
            $base = substr($base, $i);
        }
        $this->base = $base;

        $username = $authSourceData->getString('search.username', '<< unset >>');
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
        $this->password = $authSourceData->getString('search.password', '<< unset >>');
        $this->connection = $testData->getInputItem('connection');

        parent::initialize($testData);
    }

    /*
     * @return void
     */
    public function invokeTest()
    {
        try {
            $this->connection->searchfordn($this->base, $this->attributes, $this->username);
        } catch (\SimpleSAML\Error\Error $error) {
            // Fallthru
        }

        $testResult = new TestResult('LDAP Search', $this->getSubject());

        if (isSet($error)) {
            $msg = str_replace('Library - LDAP searchfordn(): ', '', $error->getMessage());
            $testResult->setState(State::ERROR);
            $testResult->setMessage($msg);
        } else {
            $testResult->setState(State::OK);
            $testResult->setMessage('Search succesful');
        }

        $this->setTestResult($testResult);
    }
}
