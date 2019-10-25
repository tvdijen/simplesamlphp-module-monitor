<?php

namespace SimpleSAML\Module\Monitor\TestCase\Cert;

use SimpleSAML\Module\Monitor\TestData;

class Data extends \SimpleSAML\Module\Monitor\TestCase\Cert
{
    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     */
    public function __construct(TestData $testData)
    {
        $certData = $testData->getInputItem('certData');

        $certData = openssl_x509_parse($certData) ?: [];
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}
