<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML_Configuration as ApplicationConfiguration;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class AuthSources extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @var ApplicationConfiguration
     */
    private $authSourceConfig;

    /**
     * @var string
     */
    private $authSourceId = '';

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration, $authSourceId)
    {
        $this->authSourceId = $authSourceId;

        $this->authSourceConfig = $configuration->getAuthSourceConfig();
        $this->setCategory('Authentication sources');

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        if ($this->checkAuthSources === true) {
            $authSources = $this->authSourceConfig->getOptions();
        } else if (is_array($this->checkAuthSources)) {
            $authSources = array_intersect($this->authSourceConfig->getOptions(), $this->checkAuthSources);
        } else { // false or invalid value
            return;
        }

        $configuration = $this->getConfiguration();
        $output = [];

        foreach ($authSources as $authSourceId) {
            $authSourceData = $this->authSourceConfig->getValue($authSourceId);
            $input = array(
                'authSourceId' => $authSourceId,
                'authSourceData' => $authSourceData
            );
            $testData = new TestData($input);
            switch ($authSourceData[0]) {
                case 'ldap:LDAP':
                    $ldapTest = new AuthSource\Ldap($configuration, $testData);
                    $this->addTestResults($ldapTest->getTestResults());
                    $output[$authSourceId] = $ldapTest->getArrayizeTestResults();
                    break;
                case 'negotiate:Negotiate':
                    $negoTest = new AuthSource\Negotiate($configuration, $testData);
                    $this->addTestResults($negoTest->getTestResults());

                    // We need to do some convertions from Negotiate > LDAP
                    $this->convertAuthSourceData($authSourceData);
                    $testData->setInput($authSourceData, 'authSourceData');
                    $ldapTest = new AuthSource\Ldap($configuration, $testData);
                    $this->addTestResults($ldapTest->getTestResults());
                    $output[$authSourceId] = array_merge($negoTest->getArrayizeTestResults() ,$ldapTest->getArrayizeTestResults());
                    break;
                case 'multiauth:MultiAuth':
                    // Relies on other authSources
                    continue 2;
                default:
                    // Not implemented
                    continue 2;
            }
        }

        $state = $this->calculateState();
        $testResult = new TestResult('Authentication sources');
        $testResult->setState($state);
        $testResult->setOutput($output);
        $this->setTestResult($testResult);
    }

    /**
     * @param array $authSourceData
     *
     * @return void
     */
    private function convertAuthSourceData(&$authSourceData)
    {
        // LDAP and Negotiate authSources use different names for equal properties
        // Hopefully this function can go away in SSP 2.0
        if (isSet($authSourceData['debugLDAP'])) {
            $authSourceData['debug'] = $authSourceData['debugLDAP'];
            unset($authSourceData['debugLDAP']);
        }
        if (isSet($authSourceData['adminUser'])) {
            $authSourceData['search.username'] = $authSourceData['adminUser'];
            unset($authSourceData['adminUser']);
        }
        if (isSet($authSourceData['adminPassword'])) {
            $authSourceData['search.password'] = $authSourceData['adminPassword'];
            unset($authSourceData['adminPassword']);
        }
        if (isSet($authSourceData['base'])) {
            $authSourceData['search.base'] = $authSourceData['base'];
            unset($authSourceData['base']);
        }
    }
}
