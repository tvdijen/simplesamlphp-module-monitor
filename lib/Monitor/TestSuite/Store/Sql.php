<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\State as State;

final class Sql extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * var string
     */
    private $host;

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $this->host = $globalConfig->getString('store.sql.dsn');
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $testResult = new TestResult('SQL', 'Overall health');

        $input = array(
            'host' => $this->host,
        );
        $testData = new TestData($input);
        $test = new TestCase\Store\Sql($testData);

        $sqlResult = $test->getTestResult();
        $this->addTestResult($sqlResult);

        $state = $this->calculateState();
        $testResult->setState($state);
        $this->setTestResult($testResult);
    }
}
