<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use \SimpleSAML\Module\monitor\TestSuiteFactory as TestSuiteFactory;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class File extends Data
{
    /**
     * @param TestSuiteFactory $testSuite
     * @param TestData $testData
     */
    public function __construct($testSuite, $testData)
    {
        $certFile = $testData->getInput('certFile');
        $certData = file_get_contents($certFile);
        $testData->setInput($certData, 'certData');

        parent::__construct($testSuite, $testData);
    }
}

