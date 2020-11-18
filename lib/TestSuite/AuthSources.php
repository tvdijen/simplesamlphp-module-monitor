<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use SimpleSAML\Configuration as ApplicationConfiguration;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Module\monitor\TestData;

final class AuthSources extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** @var \SimpleSAML\Configuration */
    private $authSourceConfig;

    /** @var array|bool */
    private $checkAuthSources;

    /** @var array|null */
    private $authSourceSpecifics;


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $moduleConfig = $configuration->getModuleConfig();
        $this->authSourceConfig = $configuration->getAuthSourceConfig();
        $this->checkAuthSources = $moduleConfig->getValue('checkAuthSources', true);
        $this->authSourceSpecifics = $moduleConfig->getValue('authSourceSpecifics', null);
        $this->setCategory('Authentication sources');

        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        if ($this->checkAuthSources === true) {
            $authSources = $this->authSourceConfig->getOptions();
        } elseif (is_array($this->checkAuthSources)) {
            $authSources = array_intersect($this->authSourceConfig->getOptions(), $this->checkAuthSources);
        } else { // false or invalid value
            return;
        }

        $configuration = $this->getConfiguration();
        $output = [];

        foreach ($authSources as $authSourceId) {
            $authSourceSpecifics = $this->getAuthSourceSpecifics($authSourceId);
            $authSourceData = $this->authSourceConfig->getValue($authSourceId);
            $input = [
                'authSourceId' => $authSourceId,
                'authSourceData' => $this->authSourceConfig->getValue($authSourceId),
                'authSourceSpecifics' => $authSourceSpecifics,
            ];
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

                    $output[$authSourceId] = array_merge(
                        $negoTest->getArrayizeTestResults(),
                        $ldapTest->getArrayizeTestResults()
                    );
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
     * @param string $authSourceId
     *
     * @return array|null
     */
    private function getAuthSourceSpecifics(string $authSourceId): ?array
    {
        if (is_array($this->authSourceSpecifics)) {
            if (array_key_exists($authSourceId, $this->authSourceSpecifics)) {
                return $this->authSourceSpecifics[$authSourceId];
            }
        }
        return null;
    }


    /**
     * @param array $authSourceData
     *
     * @return void
     */
    private function convertAuthSourceData(array &$authSourceData): void
    {
        // LDAP and Negotiate authSources use different names for equal properties
        // Hopefully this function can go away in SSP 2.0
        if (isset($authSourceData['debugLDAP'])) {
            $authSourceData['debug'] = $authSourceData['debugLDAP'];
            unset($authSourceData['debugLDAP']);
        }
        if (isset($authSourceData['adminUser'])) {
            $authSourceData['search.username'] = $authSourceData['adminUser'];
            unset($authSourceData['adminUser']);
        }
        if (isset($authSourceData['adminPassword'])) {
            $authSourceData['search.password'] = $authSourceData['adminPassword'];
            unset($authSourceData['adminPassword']);
        }
        if (isset($authSourceData['base'])) {
            $authSourceData['search.base'] = $authSourceData['base'];
            unset($authSourceData['base']);
        }
    }
}
