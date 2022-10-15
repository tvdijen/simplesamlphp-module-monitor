<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Module\monitor\State;

final class Sql extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** var string */
    private $host;


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $this->host = $globalConfig->getString('store.sql.dsn');

        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $testResult = new TestResult('SQL', 'Overall health');

        $input = [
            'host' => $this->host,
        ];
        $testData = new TestData($input);
        $test = new TestCase\Store\Sql($testData);

        $sqlResult = $test->getTestResult();
        $this->addTestResult($sqlResult);

        $state = $this->calculateState();
        $testResult->setState($state);
        $this->setTestResult($testResult);
    }
}
