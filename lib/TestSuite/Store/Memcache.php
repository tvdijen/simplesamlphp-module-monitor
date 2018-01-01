<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

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

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    protected function invokeTestSuite()
    {
        // Check Memcache-servers
        if ($this->class === null) {
            $this->setState(State::FATAL);
            $this->addMessage(State::FATAL, 'Memcache health', $this->serverGroupName, 'Missing PHP module');
        } else {
            $stats = \SimpleSAML_Memcache::getRawStats();

            foreach ($stats as $key => $serverGroup) {
                $groupName = is_numeric($key) ? '#' . ++$key : "`$key'";
                $groupTests = array();

                foreach ($serverGroup as $host => $serverStats) {
                    $input = array(
                        'serverStats' => $serverStats,
                        'host' => $host
                    );
                    $testData = new TestData($input);
                    $groupTests[] = new TestCase\Store\Memcache\Server($this, $testData);
                }

                $input = array(
                    'tests' => $groupTests,
                    'group' => $groupName
                );
                $testData = new TestData($input);
                $test = new TestCase\Store\Memcache\ServerGroup($this, $testData);
                $this->addTest($test);
            }

            $tests = $this->getTests();
            foreach ($tests as $serverGroup) {
                $this->addMessages($serverGroup->getMessages());
            }
        }

        $this->calculateState();
    }
}
