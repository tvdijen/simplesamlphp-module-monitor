<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class Metadata extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @param array
     */
    private $metadata = array();

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
        foreach ($this->metadata as $set => $metadataSet) {
            foreach ($metadataSet as $entityId => $entityMetadata) {
                $input = array(
                    'entityId' => $entityId,
                    'metadata' => $entityMetadata
                );
                $testData = new TestData($input);

                $expTest = new TestCase\Metadata\Expiration($this, $testData);
                $expTestResult = $expTest->getTestResult();
                $expTestResult->setSubject($entityId);
                $this->addTestResult($expTestResult);

                if (array_key_exists('keys', $entityMetadata)) {
                    $keys = $entityMetadata['keys'];
                    foreach ($keys as $key) {
                        $input = array(
                            'category' => $this->getType($key),
                            'certData' => "-----BEGIN CERTIFICATE-----\n" . $key['X509Certificate'] . "\n-----END CERTIFICATE-----"
                        );
                        $testData = new TestData($input);

                        $certTest = new TestCase\Cert\Data($this, $testData);
                        $certTestResult = $certTest->getTestResult();
                        $certTestResult->setSubject($entityId);
                        $this->addTestResult($certTestResult);
                    }
                }
            }
        }

        $this->calculateState();
    }


    /**
     * @param array $key
     *
     * @return string
     */
    public function getType($key)
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

