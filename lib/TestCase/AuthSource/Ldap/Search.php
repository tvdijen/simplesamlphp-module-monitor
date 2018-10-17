<?php

namespace SimpleSAML\Modules\Monitor\TestCase\AuthSource\Ldap;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Search extends \SimpleSAML\Modules\Monitor\TestCaseFactory
{
    /*
     * @var \SimpleSAML_Auth_LDAP
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
        $base = $authSourceData->getArrayizeString('search.base', '<< unset >>');
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
        } catch (\SimpleSAML_Error_Error $error) {
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
