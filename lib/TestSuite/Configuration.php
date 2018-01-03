<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Utils as Utils;

final class Configuration extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @param string|null
     */
    private $metadataCert = null;

    /**
     * @param string|null;
     */
    private $serverName = null;

    /**
     * @param integer|null;
     */
    private $serverPort = null;

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $serverVars = $configuration->getServerVars();

        $this->metadataCert = $globalConfig->getString('metadata.sign.certificate', null);
        $this->serverName = $serverVars->get('SERVER_NAME');
        $this->serverPort = $serverVars->get('SERVER_PORT');

        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    protected function invokeTestSuite()
    {
        // Check Service Communications Certificate
        if (Utils\HTTP::isHTTPS()) {
            $input = array(
                'category' => 'Service Communications Certificate',
                'hostname' => $this->serverName,
                'port' => $this->serverPort
            );
            $testData = new TestData($input);

            $test = new TestCase\Cert\Remote($this, $testData);
            $this->addTest($test);
        }

        // Check metadata signing certificate when available
        if (is_string($this->metadataCert)) {
            $input = array(
                'certFile' => Utils\Config::getCertPath($this->metadataCert),
                'category' => 'Metadata Signing Certificate'
            );
            $testData = new TestData($input);

            $test = new TestCase\Cert\File($this, $testData);
            $this->addTest($test);
        }

        $tests = $this->getTests();
        foreach ($tests as $test)
        {
            $this->addMessages($test->getMessages());
        }

        $this->calculateState();
    }
}
