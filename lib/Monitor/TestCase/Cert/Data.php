<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use \SimpleSAML\Module\monitor\TestData as TestData;

class Data extends \SimpleSAML\Module\monitor\TestCase\Cert
{
    /**
     * @param TestData $testData
     */
    public function __construct($testData)
    {
        $certData = $testData->getInputItem('certData');
        $certData = openssl_x509_parse($certData);
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}
