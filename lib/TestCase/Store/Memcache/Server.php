<?php

namespace SimpleSAML\Module\monitor\TestCase\Store\Memcache;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

final class Server extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var array|false */
    private $serverStats;


    /** @var string */
    private $host;


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->serverStats = $testData->getInputItem('serverStats');
        $this->host = $testData->getInputItem('host');

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
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
