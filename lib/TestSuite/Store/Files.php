<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Module\monitor\TestSuite\Store;

final class Files extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $this->setCategory('PHP sessions');
        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $input = [
            'path' => session_save_path(),
            'category' => 'Session storage'
        ];
        $testData = new TestData($input);

        $test = new TestCase\FileSystem\FreeSpace($testData);
        $testResult = $test->getTestResult();

        $this->addTestResult($testResult);
        $this->setTestResult($testResult);
    }
}
