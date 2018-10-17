<?php

namespace SimpleSAML\Modules\Monitor;

interface TestInterface
{
    /**
     * @return string
     */
    public function getCategory();

    /**
     * @return TestResult
     */
    public function getTestResult();

    /**
     * @return void
     */
    public function invokeTest();
}
