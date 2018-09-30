<?php

namespace SimpleSAML\Module\monitor\TestSuite\Configuration;

use \SimpleSAML\Module\monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\TestResult as TestResult;
use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Utils as Utils;

final class Database extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /**
     * @var string|null
     */
    private $store = null;

    /**
     * @var array
     */
    private $metadataSources = [];

    /**
     * @var array
     */
    private $dependentModules = ['consent'];

    /**
     * @var string
     */
    private $dsn = 'undefined';

    /**
     * @param TestConfiguration $configuration
     */
    public function __construct($configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $this->store = $globalConfig->getString('store.type', 'phpsession');
        $this->dsn = $globalConfig->getString('database.dsn');
        $this->metadataSources = $globalConfig->getArray('metadata.sources', array());

        $this->setCategory('Configuration');
        parent::__construct($configuration);
    }

    /**
     * @return void
     */
    public function invokeTest()
    {
        if ($this->store === 'sql') {
            // We use a database for session-storage
        } else if (in_array(array('type' => 'pdo'), $this->metadataSources, true)) {
            // We use a database for metadata-storage
        } else if ($this->areModulesDependingOnDatabase() === false) {
            $testResult = new TestResult('Database connection', '-');
            $testResult->setState(State::SKIPPED);
            $testResult->setMessage('Database currently not in use');
            $this->addTestResult($testResult);
            $this->setTestResult($testResult);
            return;
        } // We're using consent (TODO: but are we using consent+pdo??)

        $testData = new TestData(['dsn' => $this->dsn]);
        $connTest = new TestCase\Database\Connection($testData);
        $testResult = $connTest->getTestResult();
        $this->addTestResult($testResult);
        $this->setState($this->calculateState());
    }

    /**
     * @return bool
     */
    private function areModulesDependingOnDatabase()
    {
        foreach ($this->dependentModules as $module) {
            if (\SimpleSAML\Module::isModuleEnabled($module)) {
                return true;
            }
        }
        return false;
    }
}
