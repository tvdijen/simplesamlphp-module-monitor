<?php

namespace SimpleSAML\Module\monitor;

interface TestInterface
{
    /**
     * @return string
     */
    public function getCategory();

    /**
     * @return TestConfiguration
     */
    public function getConfiguration();

    /**
     * @return TestData
     */
    //public function getTestData();

    /**
     * @return TestResult
     */
    public function getTestResult();

    /**
     * @return void
     */
    public function invokeTest();
}
