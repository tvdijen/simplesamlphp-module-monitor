<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use \SimpleSAML\Module\monitor\TestCase as TestCase;

final class Phpsession extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /*
     * @return void
     */
    protected function initialize() {}

    /*
     * @return void
     */
    protected function invokeTestSuite()
    {
        $path = session_save_path();
        $test = new TestCase\FileSystem\FreeSpace($this, array('path' => $path));
        $this->addTest($test);

        $this->setMessages($test->getMessages());
        $this->calculateState();
    }
}
