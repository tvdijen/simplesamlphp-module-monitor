<?php

namespace SimpleSAML\Modules\Monitor\TestCase\Cert;

use \SimpleSAML\Modules\Monitor\TestData as TestData;

final class File extends Data
{
    /**
     * @param TestData $testData
     */
    public function __construct(TestData $testData)
    {
        $certFile = $testData->getInputItem('certFile');
        $certData = file_get_contents($certFile);
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}

