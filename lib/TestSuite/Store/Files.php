<?php

namespace SimpleSAML\Modules\Monitor\TestSuite\Store;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;
use \SimpleSAML\Modules\Monitor\TestSuite\Store as Store;

final class Files extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
{
    /**
     * @param TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $this->setCategory('PHP sessions');
        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
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
