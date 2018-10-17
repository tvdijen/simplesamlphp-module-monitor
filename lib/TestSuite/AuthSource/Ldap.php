<?php

namespace SimpleSAML\Modules\Monitor\TestSuite\AuthSource;

use \SimpleSAML_Configuration as ApplicationConfiguration;
use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Ldap extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
{
    /**
     * @var ApplicationConfiguration
     */
    private $authSourceData;

    /**
     * @var array|null
     */
    private $authSourceSpecifics;

    /**
     * @var string[]
     */
    private $hosts;

    /**
     * @param integer|null;
     */
    private $certExpirationWarning = null;

    /**
     * @param TestConfiguration $configuration
     * @param TestData $testData
     */
    public function __construct($configuration, $testData)
    {
        $moduleConfig = $configuration->getModuleConfig();
        $authSourceData = $testData->getInputItem('authSourceData');
        $authSourceSpecifics = $testData->getInputItem('authSourceSpecifics');

        assert(is_array($authSourceData));
        assert(is_array($authSourceSpecifics) || is_null($authSourceSpecifics));

        $authSourceData = \SimpleSAML_Configuration::loadFromArray($authSourceData);
        $this->hosts = explode(' ', $authSourceData->getString('hostname'));
        $this->authSourceData = $authSourceData;
        $this->authSourceSpecifics = $authSourceSpecifics;
        $this->certExpirationWarning = $moduleConfig->getValue('certExpirationWarning', 28);
        $this->setCategory('LDAP authentication source');

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        // Test LDAP configuration
        $confTest = new TestCase\AuthSource\Ldap\Configuration(
            new TestData(['authSourceData' => $this->authSourceData])
        );
        $confTestResult = $confTest->getTestResult();
        $this->addTestResult($confTestResult);

        if ($confTestResult->getState() === State::OK) {
            $connection = $confTestResult->getOutput('connection');

            // Test connection for each configured LDAP-server
            $failure = count($this->hosts);
            foreach ($this->hosts as $hostname) {
                $preparedTestData = $this->prepareConnection($hostname);
                $connTest = new TestCase\Network\ConnectUri(
                    new TestData($preparedTestData)
                );
                $connTestResult = $connTest->getTestResult();
                $this->addTestResult($connTestResult);

                if ($connTestResult->getState() === State::OK) {
                    $certData = $connTestResult->getOutput('certData');

                    // Test certificate when available
                    if ($certData !== null) {
                        $certTest = new TestCase\Cert(
                            new TestData([
                                'certData' => $certData,
                                'category' => 'LDAP Server Certificate',
                                'certExpirationWarning' => $this->certExpirationWarning,
                            ])
                        );
                        $certTestResult = $certTest->getTestResult();
                        $this->addTestResult($certTestResult);
                    }
                    $failure--;
                }
            }

            if ($failure === 0) {
                // Test bind
                $testData = new TestData([
                    'authSourceData' => $this->authSourceData,
                    'connection' => $connection
                ]);
                $bindTest = new TestCase\AuthSource\Ldap\Bind(
                    $testData
                );
                $bindTestResult = $bindTest->getTestResult();
                $this->addTestResult($bindTestResult);

                if ($bindTestResult->getState() === State::OK) {
                    // Test search
                    $testData = new TestData([
                        'authSourceData' => $this->authSourceData,
                        'connection' => $connection
                    ]);

                    $searchTest = new TestCase\AuthSource\Ldap\Search(
                        $testData
                    );
                    $searchTestResult = $searchTest->getTestResult();
                    $this->addTestResult($searchTestResult);
                }
            }
            unset($connection);
        }

        $state = $this->calculateState();

        $testResult = new TestResult('LDAP Authentication');
        $testResult->setState($state);
        $this->setTestResult($testResult);
    }

    /**
     * @param string $connectString
     *
     * @return array
     */
    private function prepareConnection($connectString)
    {
        $hostname = parse_url($connectString, PHP_URL_HOST);
        $authSourceData = $this->authSourceData;
        $authSourceSpecifics = $this->authSourceSpecifics;

        if (preg_match('/^(ldaps:\/\/(.*))$/', $connectString, $matches)) {
            // The default context
            $sslContext = ['capture_peer_cert' => true, 'verify_peer' => true];

            // The non-default context, if configured ...
            if (!is_null($authSourceSpecifics) && array_key_exists('ssl', $authSourceSpecifics)) {
                $sslContext = array_replace($sslContext, $authSourceSpecifics['ssl']);
            }

            $port = parse_url($connectString, PHP_URL_PORT);
            $port = $port ?: $authSourceData->getInteger('port', 636);

            $uri = 'ssl://' .  $hostname . ':' . $port;
            $context = stream_context_create(['ssl' => $sslContext]);
        } else {
            $port = $authSourceData->getInteger('port', 389);
            $uri = 'tcp://' . $hostname . ':' . $port;
            $context = stream_context_create();
        }

        $timeout = $authSourceData->getInteger('timeout', null);
        return ['uri' => $uri, 'context' => $context, 'timeout' => $timeout];
    }
}
