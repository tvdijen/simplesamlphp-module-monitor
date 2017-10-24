<?php

final class sspmod_monitor_TestCase_Cert_File extends sspmod_monitor_TestCase_Cert_Data
{
    public function __construct($testsuite, $input)
    {
        $input['certData'] = file_get_contents($input['certFile']);
        unset($input['certFile']);
        parent::__construct($testsuite, $input);
    }
}

