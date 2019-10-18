<?php

namespace SimpleSAML\Module\Monitor;

interface TestInterface
{
    /**
     * @return string
     */
    public function getCategory(): string;


    /**
     * @return \SimpleSAML\Module\Monitor\TestResult
     */
    public function getTestResult(): TestResult;


    /**
     * @return void
     */
    public function invokeTest(): void;
}
