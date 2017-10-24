<?php

use sspmod_monitor_State as State;

class sspmod_monitor_TestCase_Cert extends sspmod_monitor_TestCase
{
    private $certinfo = null;
    private $expiration = null;

    protected function initialize()
    {
        $this->setCertInfo($this->getInput('certData'));
        $this->setCategory($this->getInput('category'));
    }

    public function getSubject()
    {
        $certinfo = $this->getCertInfo();
        return $certinfo['subject']['CN'];
    }

    protected function setCertInfo($certinfo)
    {
        assert(is_array($certinfo));
        $this->certinfo = $certinfo;
    }

    protected function getCertInfo()
    {
        assert(is_array($this->certinfo));
        return $this->certinfo;
    }

    protected function getExpiration()
    {
        assert(is_int($this->expiration));
        return $this->expiration;
    }

    private function setExpiration($expiration)
    {
        assert(is_int($expiration));
        $this->expiration = $expiration;
    }

    protected function calculateExpiration()
    {
        $certinfo = $this->getCertInfo();
        $expiration = (int)(($certinfo['validTo_time_t'] - time()) / 86400);
        $this->setExpiration($expiration);
    }

    protected function invokeTest()
    {
        $this->calculateExpiration();

        $testsuite = $this->getTestSuite();
        $monitor = $testsuite->getMonitor();
        $module_config = $monitor->getModuleConfig();

        $expiration = $this->getExpiration();
        $subject = $this->getSubject();

        $days = abs($expiration);
        $days_str = $days . ' ' . (($days === 1) ? 'day' : 'days');

        $threshold = $module_config->getValue('cert_expiration_warning', 28);
        if ($expiration < 0) {
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, $this->getCategory(), $subject, 'Certificate has expired ' . $days_str . ' ago');
        } else if ($expiration <= $threshold) {
            $this->setState(State::WARNING);
            $this->addMessage(State::WARNING, $this->getCategory(), $subject, 'Certificate will expire in ' . $days_str);
        } else {
            $this->setState(State::OK);
            $this->addMessage(State::OK, $this->getCategory(), $subject, 'Certificate valid for another ' . $days_str);
        }

        $this->addOutput($expiration, 'expiration');
    }
}
