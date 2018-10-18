<?php

namespace SimpleSAML\Modules\Monitor\TestCase\Metadata;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

final class Expiration extends \SimpleSAML\Modules\Monitor\TestCaseFactory
{
    /**
     * @var string
     */
    private $entityId;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->entityId = $testData->getInputItem('entityId');
        $this->metadata = $testData->getInputItem('entityMetadata');

        parent::initialize($testData);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $testResult = new TestResult('Metadata expiration', $this->entityId);

        if (array_key_exists('expire', $this->metadata)) {
            $expiration = $this->metadata['expire'];
            if ($expiration <= time()) {
                $testResult->setState(State::ERROR);
                $testResult->setMessage('Metadata has expired');
            } else {
                $testResult->setState(State::OK);
                $testResult->setMessage('Metadata will expire on ' . strftime('%c', $expiration));
            }
        } else {
            $testResult->setState(State::OK);
            $testResult->setMessage('Metadata never expires');
        }

        $this->setTestResult($testResult);
    }
}

