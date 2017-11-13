<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_Metadata_Expiration extends sspmod_monitor_TestCase
{
    private $entityId = null;
    private $metadata = null;

    protected function initialize()
    {
        $this->entityId = $this->getInput('entityId');
        $this->metadata = $this->getInput('metadata');
    }

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

