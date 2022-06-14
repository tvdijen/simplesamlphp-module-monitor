<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Utils;

final class Configuration extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $this->setCategory('Configuration');
        parent::__construct($configuration);
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     * @return array
     */
    private function testCertificates(TestConfiguration $configuration): array
    {
        $test = new Configuration\Certificates($configuration);
        return $test->getTestResults();
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
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
