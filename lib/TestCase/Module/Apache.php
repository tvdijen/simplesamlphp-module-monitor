<?php

namespace SimpleSAML\Module\monitor\TestCase\Module;

use \SimpleSAML\Module\monitor\TestData as TestData;

final class Apache extends \SimpleSAML\Module\monitor\TestCase\Module
{
    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->setCategory('Apache');
        parent::initialize($testData);
    }
}
