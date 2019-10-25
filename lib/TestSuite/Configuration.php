<?php

namespace SimpleSAML\Module\Monitor\TestSuite;

use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Utils;

final class Configuration extends \SimpleSAML\Module\Monitor\TestSuiteFactory
{
    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $this->setCategory('Configuration');
        parent::__construct($configuration);
    }


    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
     * @return array
     */
    private function testCertificates(TestConfiguration $configuration): array
    {
        $test = new Configuration\Certificates($configuration);
        return $test->getTestResults();
    }


    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
     * @return array
     */
    private function testDatabase(TestConfiguration $configuration): array
    {
        $test = new Configuration\Database($configuration);
        return $test->getTestResults();
    }


    /**
     * @return void
     */
    public function invokeTest(): void
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
