<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class Store extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * var string|null
     */
    private $store = null;

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $this->store = $globalConfig->getString('store.type', 'phpsession');

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    protected function invokeTestSuite()
    {
        $configuration = $this->getConfiguration();

        switch ($this->store) {
            case 'phpsession':
                $test = new Store\Phpsession($configuration);
                break;
            case 'memcache':
                $test = new Store\Memcache($configuration);
                break;
// TODO:
//            case 'redis':
//            case 'redissentinel':
//                $test = new Store\Redis($configuration);
//                break;
//            case 'sql':
//                $test = new Store\Sql($configuration);
//                break;
            default:
                SimpleSAML_Logger::warning("Not implemented;  $this->store - Skipping Store TestSuite.");
                $this->setState(State::SKIPPED);
                return;
        }

        $this->addTest($test);
        $this->setMessages($test->getMessages());

        $this->calculateState();
    }
}
