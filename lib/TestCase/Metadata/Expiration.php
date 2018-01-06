<?php

namespace SimpleSAML\Module\monitor\TestCase\Metadata;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

final class Expiration extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var string|null
     */
    private $entityId = null;

    /**
     * @var array|null
     */
    private $metadata = null;

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->entityId = $testData->getInput('entityId');
        $this->metadata = $testData->getInput('metadata');

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

