<?php

namespace SimpleSAML\Module\Monitor\TestSuite\Configuration;

use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Utils;

final class Certificates extends \SimpleSAML\Module\Monitor\TestSuiteFactory
{
    /** @var string|null */
    private $metadataCert = null;

    /** @var string */
    private $serverName;

    /** @var integer */
    private $serverPort;

    /** @var integer */
    private $certExpirationWarning;


    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $moduleConfig = $configuration->getModuleConfig();
        $serverVars = $configuration->getServerVars();

        $this->metadataCert = $globalConfig->getString('metadata.sign.certificate', null);
        $this->certExpirationWarning = $moduleConfig->getValue('certExpirationWarning', 28);
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
                    "verify_peer_name" => false
                ]
            ]),
        ];

        $connTest = new TestCase\Network\ConnectUri(new TestData($input));
        $connTestResult = $connTest->getTestResult();

        $this->addTestResult($connTest->getTestResult());

        if ($connTestResult->getState() === State::OK) {
            // We were able to connect
            if (Utils\HTTP::isHTTPS()) {
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
            $input = [
                'certFile' => Utils\Config::getCertPath($this->metadataCert),
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
