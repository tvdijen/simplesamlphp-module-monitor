<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use \SimpleSAML\Module\monitor\TestSuiteFactory as TestSuiteFactory;

class Data extends \SimpleSAML\Module\monitor\TestCase\Cert
{
    /**
     * @param TestSuiteFactory $testsuite
     */
    public function __construct($testsuite, $input)
    {
        $input['certData'] = openssl_x509_parse($input['certData']);
        parent::__construct($testsuite, $input);
    }
}
