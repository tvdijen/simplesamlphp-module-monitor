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
    private $authSourceData;

    /**
     * @var string[]
     */
    private $hosts;

    /**
     * @param TestConfiguration $configuration
     * @param TestData $testData
     */
    public function __construct($configuration, $testData)
    {
        $authSourceData = $testData->getInputItem('authSourceData');
        assert(is_array($authSourceData));

        $this->authSourceData = $authSourceData;
        $this->hosts = explode(' ', $authSourceData['hostname']);
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
            $this,
            new TestData(['authSourceData' => $this->authSourceData])
        );
        $confTestResult = $confTest->getTestResult();
        $this->addTestResult($confTestResult);

        if ($confTestResult->getState() === State::OK) {
            $connection = $confTestResult->getOutput('connection');

            // Test connection for each configured LDAP-server
            foreach ($this->hosts as $hostname) {
                $preparedTestData = $this->prepareConnection($hostname, $this->authSourceData);
                $connTest = new TestCase\Network\ConnectUri(
                    $this,
                    new TestData($preparedTestData)
                );
                $connTestResult = $connTest->getTestResult();
                $this->addTestResult($connTestResult);

                if ($connTestResult->getState() === State::OK) {
                    $certData = $connTestResult->getOutput('certData');

                    // Test certificate when available
                    if ($certData !== null) {
                        $certTest = new TestCase\Cert(
                            $this,
                            new TestData(['certData' => $certData, 'category' => 'LDAP Server Certificate'])
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
                $this,
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
                    $this,
                    $testData
                );
                $searchTestResult = $searchTest->getTestResult();
                $this->addTestResult($searchTestResult);
            }
        }
    }

    /**
     * @param string $connectString
     * @param array $authSourceData
     *
     * @return array
     */
    private function prepareConnection($connectString, $authSourceData)
    {
        $hostname = parse_url($connectString, PHP_URL_HOST);

        if (preg_match('/^(ldaps:\/\/(.*))$/', $connectString, $matches)) {
            $port = parse_url($connectString, PHP_URL_PORT);
            $uri = 'ssl://' .  $hostname . ($port === null) ? '' : (':' . $port);
            $context = stream_context_create(array("ssl" => array("capture_peer_cert" => true, "verify_peer" => true)));
        } else {
            $port = $authSourceData['port'];
            $uri = 'tcp://' . $hostname . ':' . $port;
            $context = stream_context_create();
        }

        $timeout = isSet($authSourceData['timeout']) ? $authSourceData['timeout'] : null;
        return ['uri' => $uri, 'context' => $context, 'timeout' => $timeout];
    }
}
