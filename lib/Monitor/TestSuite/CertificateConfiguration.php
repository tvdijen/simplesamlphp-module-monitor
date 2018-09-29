<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Utils as Utils;

final class CertificateConfiguration extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @param string|null
     */
    private $metadataCert = null;

    /**
     * @param string|null;
     */
    private $serverName = null;

    /**
     * @param integer|null;
     */
    private $serverPort = null;

    /**
     * @param integer|null;
     */
    private $certExpirationWarning = null;

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
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
    public function invokeTest()
    {
        // Check network connection to full public URL
        $input = [
            'uri' => 'ssl://'.$this->serverName.':'.$this->serverPort,
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
            $input = array(
                'certFile' => Utils\Config::getCertPath($this->metadataCert),
                'category' => 'Metadata Signing Certificate',
                'certExpirationWarning' => $this->certExpirationWarning,
            );
            $testData = new TestData($input);

            $test = new TestCase\Cert\File($testData);
            $this->addTestResult($test->getTestResult());
        }

        $testResult = new TestResult('Configuration', '');
        $testResult->setState($this->calculateState());
        $this->setTestResult($testResult);
    }
}
