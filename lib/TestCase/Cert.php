<?php

namespace SimpleSAML\Modules\Monitor\TestCase;

use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;

class Cert extends \SimpleSAML\Modules\Monitor\TestCaseFactory
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
     * @param integer|null;
     */
    private $certExpirationWarning = null;

    /**
     * @var TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->setCategory($testData->getInputItem('category'));
        $this->setCertInfo($testData->getInputItem('certData'));
        $this->setCertExpirationWarning($testData->getInputItem('certExpirationWarning'));

        parent::initialize($testData);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        $certInfo = $this->getCertInfo();
        if (array_key_exists('CN', $certInfo['subject']) && !empty($certInfo['subject'])) {
            return 'CN='.$certInfo['subject']['CN'];
        } else {
            return 'SN='.$certInfo['serialNumber'];
        }
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
     * @param integer $certExpirationWarning
     *
     * @return void
     */
    protected function setCertExpirationWarning($certExpirationWarning)
    {
        assert(is_int($certExpirationWarning));
        $this->certExpirationWarning = $certExpirationWarning;
    }

    /**
     * @return integer|null
     */
    protected function getCertExpirationWarning()
    {
        assert(is_int($this->certExpirationWarning));
        return $this->certExpirationWarning;
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

        $threshold = $this->getCertExpirationWarning();
        $expiration = $this->getExpiration();

        $days = abs($expiration);
        $daysStr = $days . ' ' . (($days === 1) ? 'day' : 'days');

        $testResult = new TestResult($this->getCategory(), $this->getSubject());

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
