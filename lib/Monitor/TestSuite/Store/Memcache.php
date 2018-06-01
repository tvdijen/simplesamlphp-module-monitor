<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\State as State;

final class Memcache extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * var string|null
     */
    private $class = null;

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $class = class_exists('Memcache') ? 'Memcache' : (class_exists('Memcached') ? 'Memcached' : null);
        if ($class !== null) {
            $this->class = $class;
            $this->setCategory('Memcache sessions');
        }

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $testResult = new TestResult('Memcache', 'Overall health');

        if ($this->class === null) {
            $testResult->setState(State::FATAL);
            $testResult->setMessage('Missing PHP module');
            $this->addTestResult($testResult);
        } else {
            // Check Memcache-servers

            $stats = \SimpleSAML\Memcache::getRawStats();
            $i = 1;
            foreach ($stats as $key => $serverGroup) {
                $results = array();
                foreach ($serverGroup as $host => $serverStats) {
                    $input = array(
                        'serverStats' => $serverStats,
                        'host' => $host
                    );
                    $testData = new TestData($input);
                    $serverTest = new TestCase\Store\Memcache\Server($testData);
                    $results[] = $serverTest->getTestResult();
                }


                $input = array(
                    'results' => $results,
                    'group' => $i
                );
                $testData = new TestData($input);
                $groupTest = new TestCase\Store\Memcache\ServerGroup($testData);
                $groupTestResult = $groupTest->getTestResult();
                $this->addTestResult($groupTestResult);

                // Add individual server results
                $this->addTestResults($results);

                $i++;
            }

            $state = $this->calculateState();

            $testResult->setState($state);
        }
        $this->setTestResult($testResult);
    }
}
