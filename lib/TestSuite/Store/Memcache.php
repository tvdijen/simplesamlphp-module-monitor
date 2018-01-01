<?php

namespace SimpleSAML\Module\monitor\TestSuite\Store;

use \SimpleSAML\Module\monitor\TestCase as TestCase;

final class Memcache extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /*
     * @return void
     */
    protected function initialize() {}

    /*
     * @return void
     */
    protected function invokeTestSuite()
    {
        $configuration = $this->getConfiguration();
        $globalConfig = $configuration->getGlobalConfig();

        // Check Memcache-servers
        $class = class_exists('Memcache') ? 'Memcache' : (class_exists('Memcached') ? 'Memcached' : false);
        if ($class === false) {
            $serverGroups = $globalConfig->getValue('memcache_store.servers');
            $serverGroupName = array_map(function($i) {
                $tmp = array_keys($i);
                return 'Server Group #' . ++$tmp[0];
            }, $serverGroups);

            $this->setState(State::FATAL);
            $this->addMessage(State::FATAL, 'Memcache health', implode(PHP_EOL, $serverGroupName), 'Missing PHP module');
            
        } else {
            $stats = \SimpleSAML_Memcache::getRawStats();

            foreach ($stats as $key => $serverGroup) {
                $groupName = is_numeric($key) ? '#' . ++$key : "`$key'";
                $groupTests = array();

                foreach ($serverGroup as $host => $serverStats) {
                    $groupTests[] = new TestCase\Store\Memcache\Server($this, array('server_stats' => $serverStats, 'host' => $host));
                }

                $test = new TestCase\Store\Memcache\ServerGroup($this, array('tests' => $groupTests, 'group' => $groupName));
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
