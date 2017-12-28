<?php

namespace SimpleSAML\Module\monitor\TestSuite\AuthSource;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestCase as TestCase;

final class Ldap extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    private $authsourceData = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->authsourceData = $this->getInput('authsource_data');
    }

    /*
     * @return void
     */
    protected function invokeTestSuite()
    {
        // Test connection
        $hosts = explode(' ', $this->authsourceData['hostname']);
        foreach ($hosts as $host) {
            $connTest = new TestCase\AuthSource\Ldap\Connect(
                $this,
                array(
                    'authsource_data' => $this->authsourceData,
                    'hostname' => $host
                )
            );
            $this->addTest($connTest);
            $state = $connTest->getState();
            if ($state !== State::OK) {
                $this->addMessages($connTest->getMessages());
                continue;
            } else {
                $this->addMessages($connTest->getMessages());

                // Test certificate when available
                $certData = $connTest->getOutput('certData');
                if ($certData !== null) {
                    $input = array(
                        'certData' => $certData,
                        'category' => 'LDAP Server Certificate'
                    );
                    $certTest = new TestCase\Cert($this, $input);
                    $this->addTest($certTest);
                    $this->addMessages($certTest->getMessages());
                }
            }

            // Test bind
            $connection = $connTest->getOutput('connection');
            $bindTest = new TestCase\AuthSource\Ldap\Bind(
                $this,
                array(
                    'authsource_data' => $this->authsourceData,
                    'connection' => $connection
                )
            );
            $this->addTest($bindTest);
            $state = $bindTest->getState();
            if ($state === State::OK) {
                $this->addMessages($bindTest->getMessages());

                // Test search
                $searchTest = new TestCase\AuthSource\Ldap\Search(
                    $this,
                    array(
                        'authsource_data' => $this->authsourceData,
                        'connection' => $connection
                    )
                );
                $this->addTest($searchTest);
                $state = $searchTest->getState();

                if ($state === State::OK) {
                    $this->addMessages($searchTest->getMessages());
                } else {
                    $this->addMessages($searchTest->getMessages());
                }
            } else {
                $this->addMessages($bindTest->getMessages());
            }
        }

        $this->calculateState();
    }
}
