<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestSuite_AuthSource_Negotiate extends sspmod_monitor_TestSuite
{
    private $authsource_data = null;

    protected function initialize()
    {
        $this->authsource_data = $this->getInput('authsource_data');
    }

    protected function invokeTestSuite()
    {
        $test = new sspmod_monitor_TestCase_AuthSource_Negotiate($this, array('keytab' => $this->authsource_data['keytab']));
        $this->addTest($test);
        $this->addMessages($test->getMessages());
        parent::invokeTestSuite();
    }
}
