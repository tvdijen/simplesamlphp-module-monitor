<?php

namespace SimpleSAML\Module\monitor\TestCase\Store\Memcache;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class Server extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var array|null
     */
    private $serverStats;

    /**
     * @var string|null
     */
    private $host;


    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->serverStats = $testData->getInput('serverStats');
        $this->host = $testData->getInput('host');

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest()
    {
        $testResult = new TestResult('Memcache Server Health', $this->host);

        if ($this->serverStats === false) {
            $testResult->setState(State::ERROR);
            $testResult->setMessage('Host is down');
        } else {
            $bytesUsed = $this->serverStats['bytes'];
            $bytesLimit = $this->serverStats['limit_maxbytes'];
            $free = round(100.0 - (($bytesUsed / $bytesLimit) * 100));
            $testResult->addOutput($free, 'freePercentage');

            $testResult->setState(State::OK);
            $testResult->setMessage($free . '% free space');
        }

        $this->setTestResult($testResult);
    }
}
