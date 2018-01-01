<?php

namespace SimpleSAML\Module\monitor\TestCase;

use \SimpleSAML\Module\monitor\State as State;

class Cert extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    private $certinfo = null;
    private $expiration = null;


    /*
     * @return void
     */
    protected function initialize()
    {
        $this->setCertInfo($this->getInput('certData'));
        $this->setCategory($this->getInput('category'));
    }


    /*
     * @return string
     */
    public function getSubject()
    {
        $certinfo = $this->getCertInfo();
        return $certinfo['subject']['CN'];
    }


    /*
     * @return void
     */
    protected function setCertInfo($certinfo)
    {
        assert(is_array($certinfo));
        $this->certinfo = $certinfo;
    }


    /*
     * @return array|null
     */
    protected function getCertInfo()
    {
        assert(is_array($this->certinfo));
        return $this->certinfo;
    }


    /*
     * @return integer
     */
    protected function getExpiration()
    {
        assert(is_int($this->expiration));
        return $this->expiration;
    }


    /*
     * @param integer $expiration
     * 
     * @return void
     */
    private function setExpiration($expiration)
    {
        assert(is_int($expiration));
        $this->expiration = $expiration;
    }


    /*
     * @return void
     */
    protected function calculateExpiration()
    {
        $certinfo = $this->getCertInfo();
        $expiration = (int)(($certinfo['validTo_time_t'] - time()) / 86400);
        $this->setExpiration($expiration);
    }


    /*
     * @return void
     */
    protected function invokeTest()
    {
        $this->calculateExpiration();

        $testsuite = $this->getTestSuite();
        $configuration = $testsuite->getConfiguration();
        $moduleConfig = $configuration->getModuleConfig();

        $expiration = $this->getExpiration();
        $subject = $this->getSubject();

        $days = abs($expiration);
        $daysStr = $days . ' ' . (($days === 1) ? 'day' : 'days');

        $threshold = $moduleConfig->getValue('cert_expiration_warning', 28);
        if ($expiration < 0) {
            $this->setState(State::ERROR);
            $this->addMessage(State::ERROR, $this->getCategory(), $subject, 'Certificate has expired ' . $daysStr . ' ago');
        } else if ($expiration <= $threshold) {
            $this->setState(State::WARNING);
            $this->addMessage(State::WARNING, $this->getCategory(), $subject, 'Certificate will expire in ' . $daysStr);
        } else {
            $this->setState(State::OK);
            $this->addMessage(State::OK, $this->getCategory(), $subject, 'Certificate valid for another ' . $daysStr);
        }

        $this->addOutput($expiration, 'expiration');
    }
}
