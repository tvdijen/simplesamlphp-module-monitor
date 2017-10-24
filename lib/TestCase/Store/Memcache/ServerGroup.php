<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_Store_Memcache_ServerGroup extends sspmod_monitor_TestCase
{
    private $tests = null;
    private $group = null;

    protected function initialize()
    {
        $this->tests = $this->getInput('tests');
        $this->group = $this->getInput('group');
    }

    protected function invokeTest()
    {
        $states = array();
        foreach ($this->tests as $server) {
            $states[] = $server->getState();
        }
        $state = min($states);
        $this->setState($state);

        if ($state === State::OK) {
            $this->addMessage(State::OK, 'Memcache Server Group Health', 'Group ' . $this->group, 'Group is healthy');
        } elseif ($state === State::WARNING) {
            $this->addMessage(State::WARNING, 'Memcache Server Group Health', 'Group ' . $this->group, 'Group is crippled');
        } else {
            $this->addMessage(State::ERROR, 'Memcache Server Group Health', 'Group ' . $this->group, 'Group is down');
        }

        foreach ($this->tests as $server) {
            $this->addOutput($server->getOutput());
            $this->setMessages(array_merge($this->getMessages(), $server->getMessages()));
        }
    }
}
