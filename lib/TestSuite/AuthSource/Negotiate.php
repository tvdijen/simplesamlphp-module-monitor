<?php

namespace SimpleSAML\Module\Monitor\TestSuite\AuthSource;

use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;

final class Negotiate extends \SimpleSAML\Module\Monitor\TestSuiteFactory
{
    /** @var string|null */
    private $authorization;

    /** @var \KRB5NegotiateAuth */
    private $handle;


    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
     * @param \SimpleSAML\Module\Monitor\TestData $testData
     */
    public function __construct(TestConfiguration $configuration, TestData $testData)
    {
        $authSourceData = $testData->getInputItem('authSourceData');
        $serverVars = $configuration->getServerVars();

        assert(is_array($authSourceData));

        $keytab = isset($authSourceData['keytab']) ? $authSourceData['keytab'] : null;
        $this->handle = new \KRB5NegotiateAuth($keytab);
        $this->authorization = $serverVars->get('HTTP_AUTHORIZATION');
        $this->setCategory('SPNEGO authentication source');

        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $input = [
            'handle' => $this->handle,
            'authorization' => $this->authorization
        ];
        $testData = new TestData($input);

        $test = new TestCase\AuthSource\Negotiate($testData);
        $this->addTestResult($test->getTestResult());
        $this->setTestResult($test->getTestResult());
    }
}
