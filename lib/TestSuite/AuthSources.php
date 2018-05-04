<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML_Configuration as ApplicationConfiguration;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class AuthSources extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @var array|bool
     */
    private $checkAuthSources = true;

    /**
     * @var ApplicationConfiguration
     */
    private $authSourceConfig;

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $moduleConfig = $configuration->getModuleConfig();

        $this->authSourceConfig = $configuration->getAuthSourceConfig();
        $this->checkAuthSources = $moduleConfig->getValue('checkAuthSources', true);
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
                    $this->addTestResult($ldapTest->getTestResult());
                    break;
                case 'negotiate:Negotiate':
                    $negoTest = new AuthSource\Negotiate($configuration, $testData);
                    $this->addTestResult($negoTest->getTestResult());

                    // We need to do some convertions from Negotiate > LDAP
                    $this->convertAuthSourceData($authSourceData);
                    $testData->setInput($authSourceData, 'authSourceData');

                    $ldapTest = new AuthSource\Ldap($configuration, $testData);
                    $this->addTestResult($ldapTest->getTestResult());
                    break;
                case 'multiauth:MultiAuth':
                    // Relies on other authSources
                    continue 2;
                default:
                    // Not implemented
                    continue 2;
            }
        }

        $results = $test->getTestResults();
        foreach ($results as $result) {
            $this->addTestResult($result);
        }
        $this->calculateState();
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
