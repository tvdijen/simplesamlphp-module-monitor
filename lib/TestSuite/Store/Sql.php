<?php

namespace SimpleSAML\Modules\Monitor\TestSuite\Store;

use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;
use \SimpleSAML\Modules\Monitor\State as State;

final class Sql extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
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

        parent::__construct($configuration);
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
