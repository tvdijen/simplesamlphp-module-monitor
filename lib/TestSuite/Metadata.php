<?php

namespace SimpleSAML\Modules\Monitor\TestSuite;

use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Metadata extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
{
    /**
     * @param array
     */
    private $metadata = array();

    /**
     * @param integer|null;
     */
    private $certExpirationWarning = null;

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $moduleConfig = $configuration->getModuleConfig();
        $metadataConfig = $configuration->getMetadataConfig();

        $checkMetadata = $moduleConfig->getValue('checkMetadata', true);
        if ($checkMetadata === true) {
            $metadata = $metadataConfig;
        } else {
            $metadata = array();
            if (is_array($checkMetadata)) {
                foreach ($checkMetadata as $set => $entityId) {
                    if (array_key_exists($set, $metadataConfig)) {
                        if (array_key_exists($entityId, $metadataConfig[$set])) {
                            $metadata[$set][$entityId] = $metadataConfig[$set][$entityId];
                        }
                    }
                }
            }
        } 

        $this->certExpirationWarning = $moduleConfig->getValue('certExpirationWarning', 28);

        $this->fixEntityIds($metadata);
        $this->metadata = $metadata;
        $this->setCategory('Metadata');

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $configuration = $this->getConfiguration();
        $output = [];

        foreach ($this->metadata as $set => $metadataSet) {
            foreach ($metadataSet as $entityId => $entityMetadata) {
                $input = array(
                    'entityId' => $entityId,
                    'entityMetadata' => $entityMetadata
                );
                $testData = new TestData($input);

                $metadataTest = new Metadata\Entity($configuration, $testData);
                $output[$entityId] = $metadataTest->getArrayizeTestResults();

                $this->addTestResults($metadataTest->getTestResults());

            }
        }

        $state = $this->calculateState();
        $testResult = new TestResult('Metadata entities');
        $testResult->setState($state);
        $testResult->setOutput($output);
        $this->setTestResult($testResult);
    }

    /**
     * @param array $metadata
     *
     * @return void
     */
    private function fixEntityIds(&$metadata)
    {
        foreach ($metadata as $set => $metadataSet) {
            foreach ($metadataSet as $entityId => $entityMetadata) {
                if (preg_match('/__DYNAMIC(:[0-9]+)?__/', $entityId)) {
                    // Remove old entry and create a new one based on new entityId
                    unset($metadata[$set][$entityId]);
                    $newEntityId = $this->generateDynamicHostedEntityID($set);
                    $metadata[$set][$newEntityId] = $entityMetadata;
                }
            }
        }
    }

    // Borrowed this from lib/SimpleSAML/Metadata/MetaDataStorageHandlerFlatFile.php until we take care of different sources properly
    /**
     * @return string
     */
    private function generateDynamicHostedEntityID($set)
    {
        // get the configuration
        $baseurl = \SimpleSAML\Utils\HTTP::getBaseURL();

        if ($set === 'saml20-idp-hosted') {
            return $baseurl.'saml2/idp/metadata.php';
        } elseif ($set === 'shib13-idp-hosted') {
            return $baseurl.'shib13/idp/metadata.php';
        } elseif ($set === 'wsfed-sp-hosted') {
            return 'urn:federation:'.\SimpleSAML\Utils\HTTP::getSelfHost();
        } elseif ($set === 'adfs-idp-hosted') {
            return 'urn:federation:'.\SimpleSAML\Utils\HTTP::getSelfHost().':idp';
        } else {
            throw new \Exception('Can not generate dynamic EntityID for metadata of this type: ['.$set.']');
        }
    }
}

