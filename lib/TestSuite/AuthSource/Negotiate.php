<?php

namespace SimpleSAML\Module\monitor\TestSuite\AuthSource;

use \SimpleSAML\Module\monitor\TestCase as TestCase;

final class Negotiate extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    private $authsourceData = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->authsourceData = $this->getInput('authsource_data');
    }

    /*
     * @return void
     */
    protected function invokeTestSuite()
    {
        $test = new TestCase\AuthSource\Negotiate($this, array('keytab' => $this->authsourceData['keytab']));
        $this->addTest($test);
        $this->addMessages($test->getMessages());
        $this->calculateState();
    }
}
