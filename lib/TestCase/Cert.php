<?php

namespace SimpleSAML\Module\monitor\TestCase;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;

class Cert extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var array
     */
    private $certInfo = array();

    /**
     * @return integer|null
     */
    private $expiration = null;

    /**
     * @var TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->setCertInfo($testData->getInput('certData'));
        $this->setCategory($testData->getInput('category'));

        parent::initialize($testData);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        $certInfo = $this->getCertInfo();
        return $certInfo['subject']['CN'];
    }

    /**
     * @param array $certInfo
     *
     * @return void
     */
    protected function setCertInfo($certInfo)
    {
        assert(is_array($certInfo));
        $this->certInfo = $certInfo;
    }

    /**
     * @return array|null
     */
    protected function getCertInfo()
    {
        assert(is_array($this->certInfo));
        return $this->certInfo;
    }

    /**
     * @return integer
     */
    protected function getExpiration()
    {
        assert(is_int($this->expiration));
        return $this->expiration;
    }

    /**
     * @param integer $expiration
     * 
     * @return void
     */
    private function setExpiration($expiration)
    {
        assert(is_int($expiration));
        $this->expiration = $expiration;
    }

    /**
     * @return void
     */
    protected function calculateExpiration()
    {
        $certInfo = $this->getCertInfo();
        $expiration = (int)(($certInfo['validTo_time_t'] - time()) / 86400);
        $this->setExpiration($expiration);
    }

    /**
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

        $threshold = $moduleConfig->getValue('certExpirationWarning', 28);
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
