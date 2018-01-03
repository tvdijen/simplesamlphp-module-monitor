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
    private $authSource = array();

    /**
     * @var array
     */
    private $serverVars = array();

    /**
     * @var array
     */
    private $requestVars = array();

    /**
     * @param TestConfiguration $configuration
     * @param TestData $testData
     */
    public function __construct($configuration, $testData)
    {
        $authSource = $testData->getInput('authSource');
        $this->serverVars = $configuration->getServerVars();
        $this->requestVars = $configuration->getRequestVars();

        assert(is_array($authSource));
        $this->authSource = $authSource;

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    protected function invokeTestSuite()
    {
        $input = array(
            'keytab' => $this->authSource['keytab'],
            'xml' => in_array('xml', get_object_vars($this->requestVars)) ? $this->requestVars->xml : null,
            'authorization' => in_array('HTTP_AUTHORIZATION', get_object_vars($this->serverVars)) ? $this->serverVars->HTTP_AUTHORIZATION : null
        );
        $testData = new TestData($input);

        $test = new TestCase\AuthSource\Negotiate($this, $testData);
        $this->addTest($test);

        $this->addMessages($test->getMessages());
        $this->calculateState();
    }
}
