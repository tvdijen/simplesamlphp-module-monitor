<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestCase as TestCase;

final class Metadata extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /*
     * @return void
     */
    protected function initialize() {}

    /*
     * @return void
     */
    protected function invokeTestSuite()
    {
        $configuration = $this->getConfiguration();
        $moduleConfig = $configuration->getModuleConfig();
        $metadataConfig = $configuration->getMetadataConfig();
        $checkMetadata = $moduleConfig->getValue('check_metadata', true);

        if ($checkMetadata === true) {
            $metadata = $metadataConfig;
        } else if (is_array($checkMetadata)) {
            $metadata = array();
            foreach ($checkMetadata as $set => $entityId) {
                if (array_key_exists($set, $metadataConfig)) {
                    if (array_key_exists($entityId, $metadataConfig[$set])) {
                        $metadata[$set][$entityId] = $metadataConfig[$set][$entityId];
                    }
                }
            }
        } else { // false or invalid value
            return;
        }

        foreach ($metadata as $set => $metadataSet) {
            foreach ($metadataSet as $entityId => $entityMetadata) {
                if (preg_match('/__DYNAMIC(:[0-9]+)?__/', $entityId)) {
                    $entityId = $this->generateDynamicHostedEntityID($set);
                }

                $expirationTest = new TestCase\Metadata\Expiration($this, array('entityId' => $entityId, 'metadata' => $entityMetadata));
                $this->addTest($expirationTest);
                $this->addMessages($expirationTest->getMessages(), $entityId);

                if (array_key_exists('keys', $entityMetadata)) {
                    $keys = $entityMetadata['keys'];
                    foreach ($keys as $key) {
                        if ($key['encryption'] === true && $key['signing'] === false) {
                            $category = 'Encryption certificate';
                        } elseif ($key['encryption'] === false && $key['signing'] === true) {
                            $category = 'Signing certificate';
                        } else {
                            $category = 'Unknown type';
                        }

                        $input = array(
                            'category' => $category,
                            'certData' => "-----BEGIN CERTIFICATE-----\n" . $key['X509Certificate'] . "\n-----END CERTIFICATE-----"
                        );
                        $certificateTest = new TestCase\Cert\Data($this, $input);
                        $this->addTest($certificateTest);
                        $this->addMessages($certificateTest->getMessages(), $entityId);
                    }
                }
            }
        }

        $this->calculateState();
    }

    // Borrowed this from lib/SimpleSAML/Metadata/MetaDataStorageHandlerFlatFile.php until we take care of different sources properly
    /*
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

