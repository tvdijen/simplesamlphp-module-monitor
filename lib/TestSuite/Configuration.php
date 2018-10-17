<?php

namespace SimpleSAML\Modules\Monitor\TestSuite;

use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;
use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Utils as Utils;

final class Configuration extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
{
    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $this->setCategory('Configuration');
        parent::__construct($configuration);
    }

    /**
     * @return array
     */
    private function testCertificates($configuration)
    {
        $test = new Configuration\Certificates($configuration);
        return $test->getTestResults();
    }

    /**
     * @return array
     */
    private function testDatabase($configuration)
    {
        $test = new Configuration\Database($configuration);
        return $test->getTestResults();
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $configuration = $this->getConfiguration();
        $results = [];

        $results = array_merge($results, $this->testCertificates($configuration));
        $results = array_merge($results, $this->testDatabase($configuration));

        foreach ($results as $result) {
            $this->addTestResult($result);
        }
        $this->calculateState();
    }
}
