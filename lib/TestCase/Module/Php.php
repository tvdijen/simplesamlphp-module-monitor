<?php

final class sspmod_monitor_TestCase_Module_Php extends sspmod_monitor_TestCase_Module
{
    protected function initialize()
    {
        $testsuite = $this->getTestSuite();
        $this->setAvailable($testsuite->getAvailablePhpModules());
        $this->setCategory('Php');
        parent::initialize();
    }
}
