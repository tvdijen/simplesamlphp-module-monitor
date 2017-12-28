<?php

namespace SimpleSAML\Module\monitor\TestCase\Module;

final class Apache extends \SimpleSAML\Module\monitor\TestCase\Module
{
    /*
     * @return void
     */
    protected function initialize()
    {
        $testsuite = $this->getTestSuite();
        $this->setAvailable($testsuite->getAvailableApacheModules());
        $this->setCategory('Apache');
        parent::initialize();
    }
}
