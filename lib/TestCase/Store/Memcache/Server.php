<?php

namespace SimpleSAML\Module\Monitor\TestCase\Store\Memcache;

use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;

final class Server extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var array|false */
    private $serverStats;


    /** @var string */
    private $host;


    /**
     * @param \SimpleSAML\Module\Monitor\TestData $testData
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
