<?php

namespace SimpleSAML\Module\monitor\TestSuite\Metadata;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

final class Entity extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** @var array */
    private $entityMetadata;

    /** @var string */
    private $entityId;

    /** @var integer|null */
    private $certExpirationWarning = null;


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     * @param \SimpleSAML\Module\monitor\TestData $testData
     */
    public function __construct(TestConfiguration $configuration, TestData $testData)
    {
        $moduleConfig = $configuration->getModuleConfig();
        $entityMetadata = $testData->getInputItem('entityMetadata');
        $entityId = $testData->getInputItem('entityId');

        assert(is_array($entityMetadata));
        assert(is_string($entityId));

        $this->certExpirationWarning = $moduleConfig->getValue('certExpirationWarning', 28);
        $this->entityMetadata = $entityMetadata;
        $this->entityId = $entityId;

        $this->setCategory('Metadata entity');
        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $input = [
            'entityId' => $this->entityId,
            'entityMetadata' => $this->entityMetadata,
        ];
        $testData = new TestData($input);

        $expTest = new TestCase\Metadata\Expiration($testData);
        $expTestResult = $expTest->getTestResult();
        $expTestResult->setSubject($this->entityId);
        $this->addTestResult($expTestResult);

        if (array_key_exists('keys', $this->entityMetadata)) {
            $keys = $this->entityMetadata['keys'];


            $signing = array_filter($keys, [self::class, 'getSigning']);
            $encryption = array_filter($keys, [self::class, 'getEncryption']);

            foreach ($keys as $key) {
                $input = [
                    'category' => $this->getType($key),
                    'certData' => "-----BEGIN CERTIFICATE-----\n"
                        . chunk_split($key['X509Certificate'], 64)
                        . "-----END CERTIFICATE-----\n",
                    'certExpirationWarning' => $this->certExpirationWarning,
                ];
                $testData = new TestData($input);

                $certTest = new TestCase\Cert\Data($testData);
                $certTestResult = $certTest->getTestResult();

                $this->addTestResult($certTestResult);
            }
        } else {
            // saml20-idp-hosted
            $files = [];
            if (array_key_exists('certificate', $this->entityMetadata)) {
                $files[] = $this->entityMetadata['certificate'];
            }
            if (array_key_exists('new_certificate', $this->entityMetadata)) {
                $files[] = $this->entityMetadata['new_certificate'];
            }

            foreach ($files as $file) {
                $input = [
                    'category' => $this->getType(['signing' => true, 'encryption' => false]),
                    'certFile' => \SimpleSAML\Utils\Config::getCertPath($file),
                    'certExpirationWarning' => $this->certExpirationWarning,
                ];

                $testData = new TestData($input);

                $certTest = new TestCase\Cert\File($testData);
                $certTestResult = $certTest->getTestResult();

                $this->addTestResult($certTestResult);
            }
        }

        $state = $this->calculateState();

        $testResult = new TestResult('Metadata endpoint');
        $testResult->setState($state);
        $this->setTestResult($testResult);
    }


    /**
     * @param array $key
     * @return bool
     */
    private function getSigning(array $key): bool
    {
        return ($key['signing'] === true) && ($key['encryption'] === false);
    }


    /**
     * @param array $key
     * @return bool
     */
    private function getEncryption(array $key): bool
    {
        return ($key['signing'] === false) && ($key['encryption'] === true);
    }


    /**
     * @param array $key
     *
     * @return string
     */
    public function getType(array $key): string
    {
        if ($key['encryption'] === true && $key['signing'] === false) {
            $category = 'Encryption certificate';
        } elseif ($key['encryption'] === false && $key['signing'] === true) {
            $category = 'Signing certificate';
        } else {
            $category = 'Unknown type';
        }
        return $category;
    }
}
