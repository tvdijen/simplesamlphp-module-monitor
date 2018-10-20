<?php

namespace SimpleSAML\Modules\Monitor\TestSuite\Configuration;

use \SimpleSAML\Modules\Monitor\TestConfiguration as TestConfiguration;
use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\TestResult as TestResult;
use \SimpleSAML\Modules\Monitor\State as State;
use \SimpleSAML\Utils as Utils;

final class Database extends \SimpleSAML\Modules\Monitor\TestSuiteFactory
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
    public function __construct(TestConfiguration $configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $this->store = $globalConfig->getString('store.type', 'phpsession');
        $this->dsn = $globalConfig->getString('database.dsn');
        $this->metadataSources = $globalConfig->getArray('metadata.sources', []);

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
        } else if (in_array(['type' => 'pdo'], $this->metadataSources, true)) {
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
