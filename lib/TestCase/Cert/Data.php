<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use \SimpleSAML\Module\monitor\TestSuiteFactory as TestSuiteFactory;
use \SimpleSAML\Module\monitor\TestData as TestData;

class Data extends \SimpleSAML\Module\monitor\TestCase\Cert
{
    /**
     * @param TestSuiteFactory $testSuite
     * @param TestData $testData
     */
    public function __construct($testSuite, $testData)
    {
        $certData = $testData->getInput('certData');
        $certData = openssl_x509_parse($certData);
        $testData->setInput($certData, 'certData');

        parent::__construct($testSuite, $testData);
    }
}
