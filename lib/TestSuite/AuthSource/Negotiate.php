<?php

namespace SimpleSAML\Modules\Monitor\TestSuite\AuthSource;

use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;

final class Negotiate extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
{
    /** @var string|null */
    private $authorization;

    /** @var \KRB5NegotiateAuth */
    private $handle;


    /**
     * @param TestConfiguration $configuration
     * @param TestData $testData
     */
    public function __construct(TestConfiguration $configuration, TestData $testData)
    {
        $authSourceData = $testData->getInputItem('authSourceData');
        $serverVars = $configuration->getServerVars();

        assert(is_array($authSourceData));

        $keytab = isSet($authSourceData['keytab']) ? $authSourceData['keytab'] : null;
        $this->handle = new \KRB5NegotiateAuth($keytab);
        $this->authorization = $serverVars->get('HTTP_AUTHORIZATION');
        $this->setCategory('SPNEGO authentication source');

        parent::__construct($configuration);
    }


    /**
     * @return void
     */
    public function invokeTest()
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
