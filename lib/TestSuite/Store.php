<?php

namespace SimpleSAML\Modules\Monitor\TestSuite;

use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Logger as Logger;

final class Store extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
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

        if ($this->store === 'phpsession') {
            $results = $this->testPhpSession();
        } else {
            $results = $this->testSspSession($configuration);
        }

        foreach ($results as $result) {
            $this->addTestResult($result);
        }
        $this->calculateState();
    }

    /**
     * @return array
     */
    private function testSspSession($configuration)
    {
        $results = array();

        switch ($this->store) {
            case 'memcache':
                $test = new Store\Memcache($configuration);
                $results = $test->getTestResults();
                break;
//          case 'redis':
//          case 'redissentinel':
//              $test = new Store\Redis($configuration);
//              break;
            case 'sql':
                $test = new Store\Sql($configuration);
                $results = $test->getTestResults();
                break;
            default:
                Logger::warning("Not implemented;  $this->store - Skipping Store TestSuite.");
                break;
        }
        return $results;
    }

    /**
     * @return array
     */
    private function testPhpSession()
    {
        $results = array();
        switch (ini_get('session.save_handler')) {
            case 'files':
                $input = [
                    'path' => session_save_path(),
                    'category' => 'Session storage'
                ];
                $testData = new TestData($input);
                $test = new TestCase\FileSystem\FreeSpace($testData);
                $results[] = $test->getTestResult();
                break;
            case 'memcache':
            case 'memcached':
                $configuration = \SimpleSAML\Configuration::setPreLoadedConfig(
                    \SimpleSAML\Configuration::loadFromArray(
                        array(
                            'memcache_store.servers' => $this->parsePhpMemcachedConfiguration(session_save_path())
                        )
                    )
                );
                $test = new Store\Memcache($configuration);
                $results = $test->getTestResults();
                break;
//          case 'sqlite':
//          case 'mm':
            default:
                Logger::warning("Not implemented;  $this->store - Skipping Store TestSuite.");
                break;
        }
        return $results;
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
