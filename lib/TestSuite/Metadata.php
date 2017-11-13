<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestSuite_Metadata extends sspmod_monitor_TestSuite
{
    protected function invokeTestSuite()
    {
        $monitor = $this->getMonitor();
        $module_config = $monitor->getModuleConfig();
        $metadata_config = $monitor->getMetadataConfig();
        $check_metadata = $module_config->getValue('check_metadata', true);

        if ($check_metadata === true) {
            $metadata = $metadata_config;
        } else if (is_array($check_metadata)) {
            foreach ($check_metadata as $set => $entityId) {
                if (array_key_exists($set, $metadata_config)) {
                    if (array_key_exists($entityId, $metadata_config[$set])) {
                        $metadata[$set][$entityId] = $metadata_config[$set][$entityId];
                    }
                }
            }
        } else { // false or invalid value
            return;
        }

        $output = array();
        foreach ($metadata as $set => $metadata_set) {
            foreach ($metadata_set as $entityId => $entity_metadata) {
                $output = array();
                if (preg_match('/__DYNAMIC(:[0-9]+)?__/', $entityId)) {
                    $entityId = $this->generateDynamicHostedEntityID($set);
                }

                $expiration_test = new sspmod_monitor_TestCase_Metadata_Expiration($monitor, array('entityId' => $entityId, 'metadata' => $entity_metadata));
                $this->addTest($expiration_test);
                $this->addMessages($expiration_test->getMessages(), $entityId);

                if (array_key_exists('keys', $entity_metadata)) {
                    $keys = $entity_metadata['keys'];
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
                        $certificate_test = new sspmod_monitor_TestCase_Cert_Data($this, $input);
                        $this->addTest($certificate_test);
                        $this->addMessages($certificate_test->getMessages(), $entityId);
                    }
                }
            }
        }
        parent::invokeTestSuite();
    }

    // Borrowed this from lib/SimpleSAML/Metadata/MetaDataStorageHandlerFlatFile.php until we take care of different sources properly
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
            throw new Exception('Can not generate dynamic EntityID for metadata of this type: ['.$set.']');
        }
    }

}

