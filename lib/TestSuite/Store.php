<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Logger as Logger;

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
        $this->setCategory('Session store');

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
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
                Logger::warning("Not implemented;  $this->store - Skipping Store TestSuite.");
                return;
        }

        $this->addTestResult($test->getTestResult());
        $this->setTestResult($test->getTestResult());
    }
}
