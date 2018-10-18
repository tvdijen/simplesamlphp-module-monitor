<?php

namespace SimpleSAML\Modules\Monitor\TestCase\Store\Memcache;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Server extends \SimpleSAML\Modules\Monitor\TestCaseFactory
{
    /**
     * @var array|false|null
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
        $this->serverStats = $testData->getInputItem('serverStats');
        $this->host = $testData->getInputItem('host');

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
