<?php

namespace SimpleSAML\Module\Monitor\TestCase;

use \SimpleSAML\Module\Monitor\State as State;
use \SimpleSAML\Module\Monitor\TestData as TestData;
use \SimpleSAML\Module\Monitor\TestResult as TestResult;

class Passphrase extends \SimpleSAML\Module\Monitor\TestCaseFactory
{
    /** @var string */
    private $privateKey;

    /** @var string */
    private $passphrase;


    /**
     * @var TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->setCategory($testData->getInputItem('category'));
        $this->setPrivateKey($testData->getInputItem('privatekey'));
        $this->setPassphrase($testData->getInputItem('privatekey_pass'));

        parent::initialize($testData);
    }


    /**
     * @param string $privatekey
     *
     * @return void
     */
    protected function setPrivateKey(string $privateKey): void
    {
        $this->privateKey = $privateKey;
    }


    /**
     * @return string
     */
    protected function getPrivateKey(): string
    {
        return $this->privateKey;
    }


    /**
     * @param string $passphrase
     *
     * @return void
     */
    protected function setPassphrase(string $passphrase): void
    {
        $this->passphrase = $passphrase;
    }


    /**
     * @return string
     */
    protected function getPassphrase(): string
    {
        assert(is_string($this->passphrase));
        return $this->passphrase;
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $testResult = new TestResult($this->getCategory(), "Associated private key");

        if (openssl_pkey_get_private($this->privateKey, $this->passphrase)) {
            $this->setState(State::OK);
            $this->setMessage('Encrypted private key could be decrypted with the configured passphrase');
        } else {
            $this->setState(State::ERROR);
            $this->setMessage('Encrypted private key could not be decrypted with the configured passphrase');
        }

        $this->setTestResult($testResult);
    }
}
