<?php

namespace SimpleSAML\Module\monitor\TestSuite\AuthSource;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class Ldap extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @var array
     */
    private $authSource = array();

    /**
     * @var array
     */
    private $hosts = array();

    /**
     * @param TestConfiguration $configuration
     * @param TestData $testData
     */
    public function __construct($configuration, $testData)
    {
        $authSource = $testData->getInput('authSource');
        assert(is_array($authSource));

        $this->authSource = $authSource;
        $this->hosts = explode(' ', $authSource['hostname']);

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    protected function invokeTestSuite()
    {
        $hosts = $this->hosts;

        // Test connection
        foreach ($hosts as $host) {
            $input = array(
                'authSource' => $this->authSource,
                'hostname' => $host
            );
            $testData = new TestData($input);

            $connTest = new TestCase\AuthSource\Ldap\Connect(
                $this,
                $testData
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
                    $testData = new TestData($input);

                    $certTest = new TestCase\Cert($this, $testData);
                    $this->addTest($certTest);
                    $this->addMessages($certTest->getMessages());
                }
            }

            // Test bind
            $connection = $connTest->getOutput('connection');
            $input = array(
                'authSource' => $this->authSource,
                'connection' => $connection
            );
            $testData = new TestData($input);
            $bindTest = new TestCase\AuthSource\Ldap\Bind(
                $this,
                $testData
            );
            $this->addTest($bindTest);
            $state = $bindTest->getState();
            if ($state === State::OK) {
                $this->addMessages($bindTest->getMessages());

                // Test search
                $input = array(
                    'authSource' => $this->authSource,
                    'connection' => $connection
                );
                $testData = new TestData($input);

                $searchTest = new TestCase\AuthSource\Ldap\Search(
                    $this,
                    $testData
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
