<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class Phpsession extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @return void
     */
    protected function invokeTestSuite()
    {
        $input = array(
            'path' => session_save_path()
        );
        $testData = new TestData($input);

        $test = new TestCase\FileSystem\FreeSpace($this, $testData);
        $this->addTest($test);

        $this->setMessages($test->getMessages());
        $this->calculateState();
    }
}
