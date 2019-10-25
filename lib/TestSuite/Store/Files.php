<?php

namespace SimpleSAML\Module\Monitor\TestSuite\Store;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\TestSuite\Store;

final class Files extends \SimpleSAML\Module\Monitor\TestSuiteFactory
{
    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
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
