<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Utils as Utils;

final class Configuration extends \SimpleSAML\Module\monitor\TestSuiteFactory
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
