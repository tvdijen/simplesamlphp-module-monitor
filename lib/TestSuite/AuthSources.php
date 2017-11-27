<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestSuite_AuthSources extends sspmod_monitor_TestSuite
{
    protected function invokeTestSuite()
    {
        $monitor = $this->getMonitor();
        $module_config = $monitor->getModuleConfig();
        $authsource_config = $monitor->getAuthsourceConfig();
        $check_authsources = $module_config->getValue('check_authsources', true);

        if ($check_authsources === true) {
            $authsources = $authsource_config->getOptions();
        } else if (is_array($check_authsources)) {
            $authsources = array_intersect($authsource_config->getOptions(), $check_authsources);
        } else { // false or invalid value
            return;
        }

        foreach ($authsources as $authsource_id) {
            $authsource_data = $authsource_config->getValue($authsource_id);
            
            switch ($authsource_data[0]) {
                case 'ldap:LDAP':
                    $test = new sspmod_monitor_TestSuite_AuthSource_Ldap($monitor, array('authsource_id' => $authsource_id, 'authsource_data' => $authsource_data));
                    $this->addTest($test);
                    $this->addMessages($test->getMessages(), $authsource_id);
                    break;
                case 'negotiate:Negotiate':
                    $test = new sspmod_monitor_TestSuite_AuthSource_Negotiate($monitor, array('authsource_id' => $authsource_id, 'authsource_data' => $authsource_data));
                    $this->addTest($test);
                    $this->addMessages($test->getMessages(), $authsource_id);

                    // Prep authsource data
                    if (isSet($authsource_data['debugLDAP'])) {
                        $authsource_data['debug'] = $authsource_data['debugLDAP'];
                        unset($authsource_data['debugLDAP']);
                    }
                    if (isSet($authsource_data['adminUser'])) {
                        $authsource_data['search.username'] = $authsource_data['adminUser'];
                        unset($authsource_data['adminUser']);
                    }
                    if (isSet($authsource_data['adminPassword'])) {
                        $authsource_data['search.password'] = $authsource_data['adminPassword'];
                        unset($authsource_data['adminPassword']);
                    }
                    if (isSet($authsource_data['base'])) {
                        $authsource_data['search.base'] = $authsource_data['base'];
                        unset($authsource_data['base']);
                    }

                    $ldap_test = new sspmod_monitor_TestSuite_AuthSource_Ldap($monitor, array('authsource_id' => $authsource_id, 'authsource_data' => $authsource_data));
                    $this->addTest($ldap_test);
                    $this->addMessages($ldap_test->getMessages(), $authsource_id);

                    break;
                case 'multiauth:MultiAuth':
                    // Relies on other authsources
                    continue 2;
                default:
                    // Not implemented
                    continue 2;
            }
        }

        parent::invokeTestSuite();
    }
}
