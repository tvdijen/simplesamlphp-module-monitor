<?php

namespace SimpleSAML\Module\Monitor\TestSuite\Configuration;

use SimpleSAML\Module\Monitor\TestConfiguration;
use SimpleSAML\Module\Monitor\TestCase;
use SimpleSAML\Module\Monitor\TestData;
use SimpleSAML\Module\Monitor\TestResult;
use SimpleSAML\Module\Monitor\State;
use SimpleSAML\Utils;

final class Database extends \SimpleSAML\Module\Monitor\TestSuiteFactory
{
    /** @var string|null */
    private $store = null;

    /** @var array */
    private $metadataSources = [];

    /** @var array */
    private $dependentModules = ['consent'];

    /** @var string */
    private $dsn = 'undefined';


    /**
     * @param \SimpleSAML\Module\Monitor\TestConfiguration $configuration
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
    public function invokeTest(): void
    {
        if ($this->store === 'sql') {
            // We use a database for session-storage
        } elseif (in_array(['type' => 'pdo'], $this->metadataSources, true)) {
            // We use a database for metadata-storage
        } elseif ($this->areModulesDependingOnDatabase() === false) {
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
    private function areModulesDependingOnDatabase(): bool
    {
        foreach ($this->dependentModules as $module) {
            if (\SimpleSAML\Module::isModuleEnabled($module)) {
                return true;
            }
        }
        return false;
    }
}
