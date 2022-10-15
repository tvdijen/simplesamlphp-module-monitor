<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use SimpleSAML\Module\monitor\TestData;

use function file_get_contents;

final class File extends Data
{
    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     */
    public function __construct(TestData $testData)
    {
        $certFile = $testData->getInputItem('certFile');
        $certData = @file_get_contents($certFile);
        $testData->setInput($certData, 'certData');

        parent::__construct($testData);
    }
}
