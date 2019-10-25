<?php

namespace SimpleSAML\Module\Monitor\TestCase\Cert;

use SimpleSAML\Module\Monitor\TestData;

final class File extends Data
{
    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     */
    public function __construct(TestData $testData)
    {
        $certFile = $testData->getInputItem('certFile');
        $certData = @file_get_contents($certFile);
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}
