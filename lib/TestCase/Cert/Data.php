<?php

class sspmod_monitor_TestCase_Cert_Data extends sspmod_monitor_TestCase_Cert
{
    public function __construct($testsuite, $input)
    {
        $input['certData'] = openssl_x509_parse($input['certData']);
        parent::__construct($testsuite, $input);
    }
}

