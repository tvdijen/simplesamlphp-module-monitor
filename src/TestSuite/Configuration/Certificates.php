<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\TestSuite\Configuration;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Utils;

use function is_string;
use function stream_context_create;

final class Certificates extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** @var string|null */
    private ?string $metadataCert = null;

    /** @var string */
    private string $serverName;

    /** @var string|null */
    private ?string $serverPort;

    /** @var integer */
    private int $certExpirationWarning;


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $moduleConfig = $configuration->getModuleConfig();
        $serverVars = $configuration->getServerVars();

        $this->metadataCert = $globalConfig->getOptionalString('metadata.sign.certificate', null);
        $this->certExpirationWarning = $moduleConfig->getOptionalValue('certExpirationWarning', 28);
        $this->serverName = $serverVars->get('SERVER_NAME');
        $this->serverPort = $serverVars->get('SERVER_PORT');
        $this->setCategory('Configuration');

        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        // Check network connection to full public URL
        $input = [
            'uri' => 'ssl://' . $this->serverName . ':' . $this->serverPort,
            'context' => stream_context_create([
                "ssl" => [
                    "capture_peer_cert" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ]),
        ];

        $connTest = new TestCase\Network\ConnectUri(new TestData($input));
        $connTestResult = $connTest->getTestResult();

        $this->addTestResult($connTest->getTestResult());

        if ($connTestResult->getState() === State::OK) {
            $httpUtils = new Utils\HTTP();

            // We were able to connect
            if ($httpUtils->isHTTPS()) {
                // Check Service Communications Certificate
                $certData = $connTestResult->getOutput('certData');

                $input = [
                    'category' => 'Service Communications Certificate',
                    'certData' => $certData,
                    'certExpirationWarning' => $this->certExpirationWarning,
                ];

                $certTest = new TestCase\Cert(new TestData($input));
                $this->addTestResult($certTest->getTestResult());
            }
        }

        // Check metadata signing certificate when available
        if (is_string($this->metadataCert)) {
            $configUtils = new Utils\Config();

            $input = [
                'certFile' => $configUtils->getCertPath($this->metadataCert),
                'category' => 'Metadata Signing Certificate',
                'certExpirationWarning' => $this->certExpirationWarning,
            ];
            $testData = new TestData($input);

            $test = new TestCase\Cert\File($testData);
            $this->addTestResult($test->getTestResult());
        }

        $testResult = new TestResult('Configuration', '');
        $testResult->setState($this->calculateState());
        $this->setTestResult($testResult);
    }
}
