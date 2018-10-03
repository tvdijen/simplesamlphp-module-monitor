<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
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
                switch (ini_get('session.save_handler')) {
                    case 'files':
                        $input = [
                            'path' => session_save_path(),
                            'category' => 'Session storage'
                        ];
                        $testData = new TestData($input);
                        $test = new TestCase\FileSystem\FreeSpace($testData);
                        $results = array($test->getTestResult());
                        break;
                    case 'memcache':
                    case 'memcached':
                        $configuration = \SimpleSAML_Configuration::setPreLoadedConfig(
                            \SimpleSAML_Configuration::loadFromArray(
                                array(
                                    'memcache_store.servers' => $this->parsePhpMemcachedConfiguration(session_save_path())
                                )
                            )
                        );

                        $test = new Store\Memcache($configuration);
                        $results = $test->getTestResults();
                        break;
// TODO:
//                    case 'sqlite':
//                    case 'mm':
                    default:
                        Logger::warning("Not implemented;  $this->store - Skipping Store TestSuite.");
                        return;
                }
                break;
            case 'memcache':
                $test = new Store\Memcache($configuration);
                $results = $test->getTestResults();
                break;
// TODO:
//            case 'redis':
//            case 'redissentinel':
//                $test = new Store\Redis($configuration);
//                break;
            case 'sql':
                $test = new Store\Sql($configuration);
                $results = $test->getTestResults();
                break;
            default:
                Logger::warning("Not implemented;  $this->store - Skipping Store TestSuite.");
                return;

        }
        foreach ($results as $result) {
            $this->addTestResult($result);
        }
        $this->calculateState();
    }

    /**
     * @param string $spec
     *
     * @return array
     */
    private function parsePhpMemcachedConfiguration($spec)
    {
        $servers = preg_split('/\s*,\s*/', $spec);

        $results = array();
        foreach ($servers as $server) {
            $result = array();
            list($host, $params) = explode('?', $server);
            list($hostname, $port) = explode(':', $host);

            // Strip protocol when possible (memcache)
            $prefix = 'tcp://';
            if (substr($hostname, 0, 6) === $prefix) {
                $hostname = substr($hostname, 6);
            }

            $result['hostname'] = $hostname;
            if ($port !== null) {
                $result['port'] = $port;
            }
            parse_str($params, $tmp);
            $results[]  = array_merge($result, $tmp);
        }

        return array($results);
    }
}
