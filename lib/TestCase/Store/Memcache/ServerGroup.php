<?php

namespace SimpleSAML\Module\monitor\TestCase\Store\Memcache;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class ServerGroup extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var array
     */
    private $tests = array();

    /**
     * @var string|null
     */
    private $group = null;

    /**
     * @param TestData $testData
     *
     * @return void
     */
    protected function initialize($testData = null)
    {
        $this->tests = \SimpleSAML\Utils\Arrays::Arrayize($testData->getInput('tests'));
        $this->group = $testData->getInput('group');

        parent::initialize($testData);
    }

    /**
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
