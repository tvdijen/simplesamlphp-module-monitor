<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\TestSuite\Configuration;

use SimpleSAML\Module;
use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestConfiguration;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

use function in_array;

final class Database extends \SimpleSAML\Module\monitor\TestSuiteFactory
{
    /** @var string|null */
    private ?string $store = null;

    /** @var array */
    private array $metadataSources = [];

    /** @var array */
    private array $dependentModules = ['consent'];

    /** @var string */
    private string $dsn = 'undefined';


    /**
     * @param \SimpleSAML\Module\monitor\TestConfiguration $configuration
     */
    public function __construct(TestConfiguration $configuration)
    {
        $globalConfig = $configuration->getGlobalConfig();
        $this->store = $globalConfig->getOptionalString('store.type', 'phpsession');
        $this->dsn = $globalConfig->getString('database.dsn');
        $this->metadataSources = $globalConfig->getOptionalArray('metadata.sources', []);

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
            if (Module::isModuleEnabled($module)) {
                return true;
            }
        }
        return false;
    }
}
