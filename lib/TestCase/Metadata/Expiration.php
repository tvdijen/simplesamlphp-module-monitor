<?php

namespace SimpleSAML\Module\monitor\TestCase\Metadata;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;

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
    protected function invokeTest()
    {
        if (array_key_exists('expire', $this->metadata)) {
            $expiration = $this->metadata['expire'];
            if ($expiration <= time()) {
                $this->setState(State::ERROR);
                $this->addMessage(State::ERROR, 'Metadata expiration', $this->entityId, 'Metadata has expired');
            } else {
                $this->setState(State::OK);
                $this->addMessage(State::OK, 'Metadata expiration', $this->entityId, 'Metadata will expire on ' . strftime('%c', $expiration));
            }
        } else {
            $this->setState(State::OK);
            $this->addMessage(State::OK, 'Metadata expiration', $this->entityId, 'Metadata never expires');
        }
    }
}

