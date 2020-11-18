<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use SimpleSAML\Logger;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;

final class Store extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** var string|null */
    private $store = null;


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $this->store = $globalConfig->getString('store.type', 'phpsession');
        $this->setCategory('Session store');

        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $configuration = $this->getConfiguration();

        if ($this->store === 'phpsession') {
            $results = $this->testPhpSession($configuration);
        } else {
            $results = $this->testSspSession($configuration);
        }

        foreach ($results as $result) {
            $this->addTestResult($result);
        }
        $this->calculateState();
    }


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     *
     * @return array
     */
    private function testSspSession(TestConfiguration $configuration): array
    {
        $results = [];

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
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     *
     * @return array
     */
    private function testPhpSession(TestConfiguration $configuration): array
    {
        $results = [];
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
                $tmp_configuration = \SimpleSAML\Configuration::getInstance();
                $tmp_configuration = $tmp_configuration->toArray();
                $tmp_configuration['memcache_store.servers'] = $this->parsePhpMemcachedConfiguration(
                    session_save_path()
                );
                $tmp_configuration = \SimpleSAML\Configuration::loadFromArray($tmp_configuration);
                \SimpleSAML\Configuration::setPreloadedConfig($tmp_configuration);

                $test = new Store\Memcache($configuration);
                $results = $test->getTestResults();

                \SimpleSAML\Configuration::setPreloadedConfig($configuration->getGlobalConfig());
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
    private function parsePhpMemcachedConfiguration(string $spec): array
    {
        $servers = preg_split('/\s*,\s*/', $spec);

        $results = [];
        foreach ($servers as $server) {
            $result = [];
            @list($host, $params) = explode('?', $server);
            @list($hostname, $port) = explode(':', $host);

            // Strip protocol when possible (memcache)
            $prefix = 'tcp://';
            if (substr($hostname, 0, 6) === $prefix) {
                $hostname = substr($hostname, 6);
            }

            $result['hostname'] = $hostname;
            /** @psalm-suppress RedundantCondition  Remove for Psalm >= 3.6.3 */
            if (isset($port)) {
                $result['port'] = $port;
                unset($port);
            }
            parse_str($params, $tmp);
            $results[]  = array_merge($result, $tmp);
        }

        return [$results];
    }
}
