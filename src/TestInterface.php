<?php

namespace SimpleSAML\Module\monitor;

interface TestInterface
{
    /**
     * @return string
     */
    public function getCategory(): string;


    /**
     * @return \SimpleSAML\Module\monitor\TestResult
     */
    public function getTestResult(): TestResult;


    /**
     * @return void
     */
    public function invokeTest(): void;
}
