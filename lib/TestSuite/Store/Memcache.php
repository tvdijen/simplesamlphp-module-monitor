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
     * var string
     */
    private $serverGroupName = '** Unknown **';

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $class = class_exists('Memcache') ? 'Memcache' : (class_exists('Memcached') ? 'Memcached' : null);
        if ($class === null) {
            $globalConfig = $configuration->getGlobalConfig();
            $serverGroups = $globalConfig->getValue('memcache_store.servers', array());
            $serverGroupName = array_map(function($i) {
                $group = array_keys($i);
                return 'Server Group #' . ++$group[0];
            }, $serverGroups);
            $this->serverGroupName = implode(PHP_EOL, $serverGroupName);
        }
        $this->class = $class;
        $this->setCategory('Memcache sessions');

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $testResult = new TestResult('Memcache health', $this->serverGroupName);

        // Check Memcache-servers
        if ($this->class === null) {
            $testResult->setState(State::FATAL);
            $testResult->setMessage('Missing PHP module');
        } else {
            $stats = \SimpleSAML_Memcache::getRawStats();
            foreach ($stats as $key => $serverGroup) {
                foreach ($serverGroup as $host => $serverStats) {
                    $input = array(
                        'serverStats' => $serverStats,
                        'host' => $host
                    );
                    $testData = new TestData($input);
                    $groupTest = new TestCase\Store\Memcache\Server($testData);
                    $this->addTestResult($groupTest->getTestResult());
                }

                $state = $this->calculateState();
                $testResult->setState($state);
                if ($state === State::OK) {
                    $testResult->setMessage('Group is healthy');
                } elseif ($state === State::WARNING) {
                    $testResult->setMessage('Group is crippled');
                } else {
                    $testResult->setMessage('Group is down');
                }
            }
        }
        $this->setTestResult($testResult);
    }
}
