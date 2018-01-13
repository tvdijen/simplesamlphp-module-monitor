<?php

namespace SimpleSAML\Module\monitor\TestSuite\AuthSource;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class Negotiate extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @var array
     */
    private $authSourceData = array();

    /**
     * @var bool|null
     */
    private $xml = null;

    /**
     * @var string|null
     */
    private $authorization = null;

    /**
     * @param TestConfiguration $configuration
     * @param TestData $testData
     */
    public function __construct($configuration, $testData)
    {
        $authSourceData = $testData->getInputItem('authSourceData');
        $serverVars = $configuration->getServerVars();
        $requestVars = $configuration->getRequestVars();

        assert(is_array($authSourceData));

        $this->authSourceData = $authSourceData;
        $this->authorization = $serverVars->get('HTTP_AUTHORIZATION');
        $this->xml = $requestVars->get('xml');
        $this->setCategory('SPNEGO authentication source');

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        $input = array(
            'keytab' => $this->authSourceData['keytab'],
            'xml' => $this->xml,
            'authorization' => $this->authorization
        );
        $testData = new TestData($input);

        $test = new TestCase\AuthSource\Negotiate($this, $testData);
        $this->addTestResult($test->getTestResult());
        $this->setTestResult($test->getTestResult());
    }
}
