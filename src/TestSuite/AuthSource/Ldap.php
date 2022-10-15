<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\TestSuite\AuthSource;

use SimpleSAML\Assert\Assert;
use SimpleSAML\Configuration;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

use function array_key_exists;
use function array_replace;
use function explode;
use function is_null;
use function parse_url;
use function preg_match;
use function preg_split;
use function stream_context_create;

final class Ldap extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** @var \SimpleSAML\Configuration */
    private Configuration $authSourceData;

    /** @var array|null */
    private ?array $authSourceSpecifics;

    /** @var string[] */
    private array $hosts;

    /** @var integer|null */
    private ?int $certExpirationWarning = null;


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     * @param \SimpleSAML\Module\monitor\TestData $testData
     */
    public function __construct(TestConfiguration $configuration, TestData $testData)
    {
        $moduleConfig = $configuration->getModuleConfig();
        $authSourceData = $testData->getInputItem('authSourceData');
        $authSourceSpecifics = $testData->getInputItem('authSourceSpecifics');

        Assert::isArray($authSourceData);
        Assert::nullOrIsArray($authSourceSpecifics);

        $authSourceData = Configuration::loadFromArray($authSourceData);
        $this->hosts = preg_split('/\s+/', $authSourceData->getString('hostname'), -1, PREG_SPLIT_NO_EMPTY);
        $this->authSourceData = $authSourceData;
        $this->authSourceSpecifics = $authSourceSpecifics;
        $this->certExpirationWarning = $moduleConfig->getOptionalValue('certExpirationWarning', 28);
        $this->setCategory('LDAP authentication source');

        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
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
                $bindTest = new TestCase\AuthSource\Ldap\Bind(
                    new TestData([
                        'authSourceData' => $this->authSourceData,
                        'connection' => $connection
                    ])
                );
                $bindTestResult = $bindTest->getTestResult();
                $this->addTestResult($bindTestResult);

                if ($bindTestResult->getState() === State::OK) {
                    // Test search
                    $searchTest = new TestCase\AuthSource\Ldap\Search(
                        new TestData([
                            'authSourceData' => $this->authSourceData,
                            'connection' => $connection
                        ])
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
    private function prepareConnection(string $connectString): array
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
