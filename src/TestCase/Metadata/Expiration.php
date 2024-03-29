<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\TestCase\Metadata;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

use function array_key_exists;
use function date;

final class Expiration extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var string */
    private string $entityId;

    /** @var array */
    private array $metadata;


    /**
     * @param \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->entityId = $testData->getInputItem('entityId');
        $this->metadata = $testData->getInputItem('entityMetadata');

        parent::initialize($testData);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $testResult = new TestResult('Metadata expiration', $this->entityId);

        if (array_key_exists('expire', $this->metadata)) {
            $expiration = $this->metadata['expire'];
            if ($expiration <= time()) {
                $testResult->setState(State::ERROR);
                $testResult->setMessage('Metadata has expired');
            } else {
                $testResult->setState(State::OK);
                $testResult->setMessage('Metadata will expire on ' . date('r', $expiration));
            }
        } else {
            $testResult->setState(State::OK);
            $testResult->setMessage('Metadata never expires');
        }

        $this->setTestResult($testResult);
    }
}
