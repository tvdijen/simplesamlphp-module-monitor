<?php

namespace SimpleSAML\Module\monitor\TestCase;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

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
        $this->setCertInfo($testData->getInputItem('certData'));
        $this->setCategory($testData->getInputItem('category'));

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
    public function invokeTest()
    {
        $this->calculateExpiration();

        $testsuite = $this->getTestSuite();
        $configuration = $testsuite->getConfiguration();
        $moduleConfig = $configuration->getModuleConfig();

        $expiration = $this->getExpiration();

        $days = abs($expiration);
        $daysStr = $days . ' ' . (($days === 1) ? 'day' : 'days');

        $testResult = new TestResult($this->getCategory(), $this->getSubject());
        $threshold = $moduleConfig->getValue('certExpirationWarning', 28);

        if ($expiration < 0) {
            $testResult->setState(State::ERROR);
            $testResult->setMessage('Certificate has expired ' . $daysStr . ' ago');
        } else if ($expiration <= $threshold) {
            $testResult->setState(State::WARNING);
            $testResult->setMessage('Certificate will expire in ' . $daysStr);
        } else {
            $testResult->setState(State::OK);
            $testResult->setMessage('Certificate valid for another ' . $daysStr);
        }

        $testResult->addOutput($expiration, 'expiration');
        $this->setTestResult($testResult);
    }
}
