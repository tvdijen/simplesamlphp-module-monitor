<?php

namespace SimpleSAML\Module\monitor\TestCase\Module;

final class Php extends \SimpleSAML\Module\monitor\TestCase\Module
{
    /*
     * @return void
     */
    protected function initialize()
    {
        $testsuite = $this->getTestSuite();
        $this->setAvailable($testsuite->getAvailablePhpModules());
        $this->setCategory('Php');
        parent::initialize();
    }
}
