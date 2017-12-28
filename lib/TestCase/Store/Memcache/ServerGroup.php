<?php

namespace SimpleSAML\Module\monitor\TestCase\Store\Memcache;

use \SimpleSAML\Module\monitor\State as State;

final class ServerGroup extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $tests = null;
    private $group = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->tests = $this->getInput('tests');
        $this->group = $this->getInput('group');
    }

    /*
     * @return void
     */
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
