<?php

final class sspmod_monitor_TestSuite_Store_Memcache extends sspmod_monitor_TestSuite
{
    protected function invokeTestSuite()
    {
        $monitor = $this->getMonitor();
        $global_config = $monitor->getGlobalConfig();

        // Check Memcache-servers
        $class = class_exists('Memcache') ? 'Memcache' : (class_exists('Memcached') ? 'Memcached' : false);
        if ($class === false) {
            $server_groups = $global_config->getValue('memcache_store.servers');
            $server_groups_name = array_map(function($i) {
                $tmp = array_keys($i);
                return 'Server Group #' . ++$tmp[0];
            }, $server_groups);

            $this->setState(State::FATAL);
            $this->addMessage(State::FATAL, 'Memcache health', implode(PHP_EOL, $server_groups_name), 'Missing PHP module');
            
        } else {
            $stats = SimpleSAML_Memcache::getRawStats();

            foreach ($stats as $key => $server_group) {
                $group_name = is_numeric($key) ? '#' . ++$key : "`$key'";
                $group_tests = array();

                foreach ($server_group as $host => $server_stats) {
                    $group_tests[] = new sspmod_monitor_TestCase_Store_Memcache_Server($this, array('server_stats' => $server_stats, 'host' => $host));
                }

                $test = new sspmod_monitor_TestCase_Store_Memcache_ServerGroup($this, array('tests' => $group_tests, 'group' => $group_name));
                $this->addTest($test);
            }

            $tests = $this->getTests();
            foreach ($tests as $server_group) {
                $this->addMessages($server_group->getMessages());
            }
        }

        parent::invokeTestSuite();
    }
}
