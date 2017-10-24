<?php

final class sspmod_monitor_TestCase_Module_Apache extends sspmod_monitor_TestCase_Module
{
    protected function initialize()
    {
        $testsuite = $this->getTestSuite();
        $this->setAvailable($testsuite->getAvailableApacheModules());
        $this->setCategory('Apache');
        parent::initialize();
    }
}
