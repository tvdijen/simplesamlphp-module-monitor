<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class Phpsession extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
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

        $test = new TestCase\FileSystem\FreeSpace($this, $testData);
        $testResult = $test->getTestResult();
        $this->addTestResult($testResult);

        $this->setTestResult($testResult);
    }
}
