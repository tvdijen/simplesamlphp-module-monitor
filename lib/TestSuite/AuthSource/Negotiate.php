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
     * @param TestConfiguration|null $configuration
     * @param TestData $testData
     */
    public function __construct($configuration = null, $testData)
    {
        $authSource = $testData->getInput('authSource');

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
            'keytab' => $this->authSource['keytab']
        );
        $testData = new TestData($input);

        $test = new TestCase\AuthSource\Negotiate($this, $testData);
        $this->addTest($test);

        $this->addMessages($test->getMessages());
        $this->calculateState();
    }
}
