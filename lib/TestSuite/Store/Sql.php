<?php

namespace SimpleSAML\Module\Monitor\TestSuite\Store;

use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\State;

final class Sql extends \SimpleSAML\Module\Monitor\TestSuiteFactory
{
    /** var string */
    private $host;


    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
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
