<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use \SimpleSAML\Module\monitor\TestData as TestData;

final class File extends Data
{
    /**
     * @param TestData $testData
     */
    public function __construct($testData)
    {
        $certFile = $testData->getInputItem('certFile');
        $certData = file_get_contents($certFile);
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}

