<?php

namespace SimpleSAML\Module\monitor\TestSuite\AuthSource;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class Negotiate extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @var string|null
     */
    private $authorization;

    /**
     * @var KRB5NegotiateAuth
     */
    private $handle;

    /**
     * @param TestConfiguration $configuration
     * @param TestData $testData
     */
    public function __construct($configuration, $testData)
    {
        $authSourceData = $testData->getInputItem('authSourceData');
        $serverVars = $configuration->getServerVars();

        assert(is_array($authSourceData));

        $keytab = $authSourceData->getString('keytab', null);
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
        $input = array(
            'handle' => $this->handle,
            'authorization' => $this->authorization
        );
        $testData = new TestData($input);

        $test = new TestCase\AuthSource\Negotiate($testData);
        $this->addTestResult($test->getTestResult());
        $this->setTestResult($test->getTestResult());
    }
}
