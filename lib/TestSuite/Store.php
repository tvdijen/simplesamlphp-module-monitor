<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestSuite_Store extends sspmod_monitor_TestSuite
{
    protected function invokeTestSuite()
    {
        $monitor = $this->getMonitor();
        $global_config = $monitor->getGlobalConfig();

        $store = $global_config->getString('store.type');
        switch ($store) {
            case 'phpsession':
                $test = new sspmod_monitor_TestSuite_Store_Phpsession($monitor, array());
                break;
            case 'memcache':
                $test = new sspmod_monitor_TestSuite_Store_Memcache($monitor, array());
                break;
// TODO:
//            case 'redis':
//            case 'redissentinel':
//                $test = new sspmod_monitor_TestSuite_Store_Redis($monitor, array());
//                break;
//            case 'sql':
//                $test = new sspmod_monitor_TestSuite_Store_Sql($monitor, array());
//                break;
            default:
                SimpleSAML_Logger::warning("Not implemented;  $store - Skipping Store TestSuite.");
                $this->setState(State::SKIPPED);
                return;
        }

        $this->addTest($test);
        $this->setMessages($test->getMessages());

        parent::invokeTestSuite();
    }
}
