<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestSuite_AuthSource_Ldap extends sspmod_monitor_TestSuite
{
    private $authsource_data = null;

    protected function initialize()
    {
        $this->authsource_data = $this->getInput('authsource_data');
    }

    protected function invokeTestSuite()
    {
        // Test connection
        $hosts = explode(' ', $this->authsource_data['hostname']);
        foreach ($hosts as $host) {
            $conn_test = new sspmod_monitor_TestCase_AuthSource_Ldap_Connect(
                $this,
                array(
                    'authsource_data' => $this->authsource_data,
                    'hostname' => $host
                )
            );
            $this->addTest($conn_test);
            $state = $conn_test->getState();
            if ($state !== State::OK) {
                $this->addMessages($conn_test->getMessages());
                continue;
            } else {
                $this->addMessages($conn_test->getMessages());

                // Test certificate when available
                $certData = $conn_test->getOutput('certData');
                if ($certData !== null) {
                    $input = array(
                        'certData' => $certData,
                        'category' => 'LDAP Server Certificate'
                    );
                    $cert_test = new sspmod_monitor_TestCase_Cert($this, $input);
                    $this->addTest($cert_test);
                    $this->addMessages($cert_test->getMessages());
                }
            }

            // Test bind
            $connection = $conn_test->getOutput('connection');
            $bind_test = new sspmod_monitor_TestCase_AuthSource_Ldap_Bind(
                $this,
                array(
                    'authsource_data' => $this->authsource_data,
                    'connection' => $connection
                )
            );
            $this->addTest($bind_test);
            $state = $bind_test->getState();
            if ($state === State::OK) {
                $this->addMessages($bind_test->getMessages());

                // Test search
                $search_test = new sspmod_monitor_TestCase_AuthSource_Ldap_Search(
                    $this,
                    array(
                        'authsource_data' => $this->authsource_data,
                        'connection' => $connection
                    )
                );
                $this->addTest($search_test);
                $state = $search_test->getState();

                if ($state === State::OK) {
                    $this->addMessages($search_test->getMessages());
                } else {
                    $this->addMessages($search_test->getMessages());
                }
            } else {
                $this->addMessages($search_test->getMessages());
            }
        }

        parent::invokeTestSuite();
    }
}
