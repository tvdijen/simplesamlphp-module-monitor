<?php

namespace SimpleSAML\Module\monitor\TestSuite\AuthSource;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class Ldap extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @var array
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

        $this->hosts = explode(' ', $authSourceData['hostname']);
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
            foreach ($this->hosts as $hostname) {
                $preparedTestData = $this->prepareConnection($hostname, $this->authSourceData, $this->authSourceSpecifics);
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
                }
            }

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

        $state = $this->calculateState();

        $testResult = new TestResult('LDAP Authentication');
        $testResult->setState($state);
        $this->setTestResult($testResult);
    }

    /**
     * @param string $connectString
     * @param array $authSourceData
     * @param array|null $authSourceSpecifics
     *
     * @return array
     */
    private function prepareConnection($connectString, $authSourceData, $authSourceSpecifics)
    {
        $hostname = parse_url($connectString, PHP_URL_HOST);

        if (preg_match('/^(ldaps:\/\/(.*))$/', $connectString, $matches)) {
            // The default context
            $sslContext = ['capture_peer_cert' => true, 'verify_peer' => true];

            // The non-default context, if configured ...
            if (!is_null($authSourceSpecifics) && array_key_exists('ssl', $authSourceSpecifics)) {
                $sslContext = array_replace($sslContext, $authSourceSpecifics['ssl']);
            }

            $port = parse_url($connectString, PHP_URL_PORT);
            $port = $port ?: $authSourceData['port'];

            $uri = 'ssl://' .  $hostname . ':' . $port;
            $context = stream_context_create(['ssl' => $sslContext]);
        } else {
            $port = $authSourceData['port'];
            $uri = 'tcp://' . $hostname . ':' . $port;
            $context = stream_context_create();
        }

        $timeout = isSet($authSourceData['timeout']) ? $authSourceData['timeout'] : null;
        return ['uri' => $uri, 'context' => $context, 'timeout' => $timeout];
    }
}
