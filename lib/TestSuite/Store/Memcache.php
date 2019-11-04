<?php

namespace SimpleSAML\Module\Monitor\TestSuite\Store;

use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\State;

final class Memcache extends \SimpleSAML\Module\Monitor\TestSuiteFactory
{
    /** var string|null */
    private $class = null;


    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
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
    public function invokeTest(): void
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
                $results = [];
                foreach ($serverGroup as $host => $serverStats) {
                    $input = [
                        'serverStats' => $serverStats,
                        'host' => $host
                    ];
                    $testData = new TestData($input);
                    $serverTest = new TestCase\Store\Memcache\Server($testData);
                    $results[] = $serverTest->getTestResult();
                }


                $input = [
                    'results' => $results,
                    'group' => strval($i)
                ];
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
