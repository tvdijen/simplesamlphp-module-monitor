<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\State as State;

final class Store extends \SimpleSAML\Module\monitor\TestSuiteFactory
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

        $store = $globalConfig->getString('store.type');
        switch ($store) {
            case 'phpsession':
                $test = new Store\Phpsession($configuration, array());
                break;
            case 'memcache':
                $test = new Store\Memcache($configuration, array());
                break;
// TODO:
//            case 'redis':
//            case 'redissentinel':
//                $test = new Store\Redis($monitor, array());
//                break;
//            case 'sql':
//                $test = new Store\Sql($monitor, array());
//                break;
            default:
                SimpleSAML_Logger::warning("Not implemented;  $store - Skipping Store TestSuite.");
                $this->setState(State::SKIPPED);
                return;
        }

        $this->addTest($test);
        $this->setMessages($test->getMessages());

        $this->calculateState();
    }
}
