<?php

namespace SimpleSAML\Module\monitor\TestSuite;

final class AuthSources extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /*
     * @return void
     */
    protected function initialize() {}

    /*
     * @return void
     */
    protected function invokeTestSuite()
    {
        $configuration = $this->getConfiguration();
        $moduleConfig = $configuration->getModuleConfig();
        $authsourceConfig = $configuration->getAuthsourceConfig();
        $checkAuthsources = $moduleConfig->getValue('check_authsources', true);

        if ($checkAuthsources === true) {
            $authsources = $authsourceConfig->getOptions();
        } else if (is_array($checkAuthsources)) {
            $authsources = array_intersect($authsourceConfig->getOptions(), $checkAuthsources);
        } else { // false or invalid value
            return;
        }

        foreach ($authsources as $authsourceId) {
            $authsourceData = $authsourceConfig->getValue($authsourceId);
            
            switch ($authsourceData[0]) {
                case 'ldap:LDAP':
                    $test = new AuthSource\Ldap($configuration, array('authsource_id' => $authsourceId, 'authsource_data' => $authsourceData));
                    $this->addTest($test);
                    $this->addMessages($test->getMessages(), $authsourceId);
                    break;
                case 'negotiate:Negotiate':
                    $test = new AuthSource\Negotiate($configuration, array('authsource_id' => $authsourceId, 'authsource_data' => $authsourceData));
                    $this->addTest($test);
                    $this->addMessages($test->getMessages(), $authsourceId);

                    // Prep authsource data
                    if (isSet($authsourceData['debugLDAP'])) {
                        $authsourceData['debug'] = $authsourceData['debugLDAP'];
                        unset($authsourceData['debugLDAP']);
                    }
                    if (isSet($authsourceData['adminUser'])) {
                        $authsourceData['search.username'] = $authsourceData['adminUser'];
                        unset($authsourceData['adminUser']);
                    }
                    if (isSet($authsourceData['adminPassword'])) {
                        $authsourceData['search.password'] = $authsourceData['adminPassword'];
                        unset($authsourceData['adminPassword']);
                    }
                    if (isSet($authsourceData['base'])) {
                        $authsourceData['search.base'] = $authsourceData['base'];
                        unset($authsourceData['base']);
                    }

                    $ldapTest = new AuthSource\Ldap($configuration, array('authsource_id' => $authsourceId, 'authsource_data' => $authsourceData));
                    $this->addTest($ldapTest);
                    $this->addMessages($ldapTest->getMessages(), $authsourceId);

                    break;
                case 'multiauth:MultiAuth':
                    // Relies on other authsources
                    continue 2;
                default:
                    // Not implemented
                    continue 2;
            }
        }

        $this->calculateState();
    }
}
