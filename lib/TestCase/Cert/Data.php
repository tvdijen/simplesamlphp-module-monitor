<?php

namespace SimpleSAML\Modules\Monitor\TestCase\Cert;

use \SimpleSAML\Modules\Monitor\TestData as TestData;

class Data extends \SimpleSAML\Modules\Monitor\TestCase\Cert
{
    /**
     * @param TestData $testData
     */
    public function __construct(TestData $testData)
    {
        $certData = $testData->getInputItem('certData');

        $certData = openssl_x509_parse($certData) ?: [];
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}
