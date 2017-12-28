<?php

namespace SimpleSAML\Module\monitor\TestCase\Store\Memcache;

use \SimpleSAML\Module\monitor\State as State;

final class Server extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $host = null;
    private $serverStats = null;

    /*
     * @return void
     */
    protected function initialize()
    {
        $this->serverStats = $this->getInput('server_stats');
        $this->host = $this->getInput('host');
    }

    /*
     * @return void
     */
    protected function invokeTest()
    {
        if ($this->serverStats === false) {
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, 'Memcache Server Health', $this->host, 'Host is down');
        } else {
            $bytesUsed = $this->serverStats['bytes'];
            $bytesLimit = $this->serverStats['limit_maxbytes'];
            $free = round(100.0 - (($bytesUsed / $bytesLimit) * 100));
            $this->addOutput($free, 'free_percentage');

            $this->setState(State::OK);
            $this->addMessage(State::OK, 'Memcache Server Health', $this->host, $free . '% free space');
        }
    }
}
