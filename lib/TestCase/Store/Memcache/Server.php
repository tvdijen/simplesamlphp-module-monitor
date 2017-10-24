<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_Store_Memcache_Server extends sspmod_monitor_TestCase
{
    private $host = null;
    private $server_stats = null;

    protected function initialize()
    {
        $this->server_stats = $this->getInput('server_stats');
        $this->host = $this->getInput('host');
    }

    protected function invokeTest()
    {
        if ($this->server_stats === false) {
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'Memcache Server Health', $this->host, 'Host is down');
        } else {
            $bytes_used = $this->server_stats['bytes'];
            $bytes_limit = $this->server_stats['limit_maxbytes'];
            $free = round(100.0 - (($bytes_used / $bytes_limit) * 100));
            $this->addOutput($free, 'free_percentage');

            $this->setState(State::OK);
            $this->addMessage(State::OK, 'Memcache Server Health', $this->host, $free . '% free space');
        }
    }
}
