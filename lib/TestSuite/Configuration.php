<?php

namespace SimpleSAML\Module\monitor\TestSuite;

use \SimpleSAML\Module\monitor\TestCase as TestCase;

final class Configuration extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /*
     * @return void
     */
    protected function initialize() {}

    /*
     * @return void
     */
    protected function invokeTestSuite()
    {
        $monitor = $this->getMonitor();
        $globalConfig = $monitor->getGlobalConfig();
        // Check Service Communications Certificate
        if (\SimpleSAML\Utils\HTTP::isHTTPS()) {
            $input = array(
                'category' => 'Service Communications Certificate',
                'hostname' => $_SERVER['SERVER_NAME'],
                'port' => $_SERVER['SERVER_PORT']
            );

            $test = new TestCase\Cert\Remote($this, $input);
            $this->addTest($test);
        }

        // Check metadata signing certificate when available
        if ($globalConfig->hasValue('metadata.sign.certificate')) {
            $metadataCert = $globalConfig->getString('metadata.sign.certificate');

            $input = array(
                'certFile' => \SimpleSAML\Utils\Config::getCertPath($metadataCert),
                'category' => 'Metadata Signing Certificate'
            );

            $test = new TestCase\Cert\File($this, $input);
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
