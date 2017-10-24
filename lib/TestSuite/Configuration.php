<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestSuite_Configuration extends sspmod_monitor_TestSuite
{
    protected function invokeTestSuite()
    {
        $monitor = $this->getMonitor();
        $global_config = $monitor->getGlobalConfig();
        // Check Service Communications Certificate
        if (\SimpleSAML\Utils\HTTP::isHTTPS()) {
            $input = array(
                'category' => 'Service Communications Certificate',
                'hostname' => $_SERVER['SERVER_NAME'],
                'port' => $_SERVER['SERVER_PORT']
            );

            $test = new sspmod_monitor_TestCase_Cert_Remote($this, $input);
            $this->addTest($test);
        }

        // Check metadata signing certificate when available
        if ($global_config->hasValue('metadata.sign.certificate')) {
            $metadata_cert = $global_config->getString('metadata.sign.certificate');

            $input = array(
                'certFile' => \SimpleSAML\Utils\Config::getCertPath($metadata_cert),
                'category' => 'Metadata Signing Certificate'
            );

            $test = new sspmod_monitor_TestCase_Cert_File($this, $input);
            $this->addTest($test);
        }

        $tests = $this->getTests();
        foreach ($tests as $test)
        {
            $this->addMessages($test->getMessages());
        }
        parent::invokeTestSuite();
    }
}
