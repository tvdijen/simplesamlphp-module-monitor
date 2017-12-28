<?php

namespace SimpleSAML\Module\monitor\TestCase\Cert;

use \SimpleSAML\Module\monitor\TestSuiteFactory as TestSuiteFactory;

final class File extends Data
{
    /**
     * @param TestSuiteFactory $testsuite
     */
    public function __construct($testsuite, $input)
    {
        $input['certData'] = file_get_contents($input['certFile']);
        unset($input['certFile']);
        parent::__construct($testsuite, $input);
    }
}

