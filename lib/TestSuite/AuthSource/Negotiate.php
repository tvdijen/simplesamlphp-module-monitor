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
        $hosts = explode(' ', $this->authsource_data['hostname']);
        foreach ($hosts as $host) {
            $test = new sspmod_monitor_TestCase_AuthSource_Negotiate($this, array('authsource_data' => $this->authsource_data, 'hostname' => $host));
            $this->addTest($test);

            $state = $test->getState();
            if ($state !== State::OK) {
                $this->addMessages($test->getMessages());
                continue;
            } else {
                $this->addMessages($test->getMessages());
            }
        }
        parent::invokeTestSuite();
    }
}
