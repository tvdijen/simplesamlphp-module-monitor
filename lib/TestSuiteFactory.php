<?php

namespace SimpleSAML\Module\monitor;

abstract class TestSuiteFactory extends TestFactory
{
    /**
     * @var array|null
     */
    private $tests = null;

    /**
     * @param TestConfiguration|null $configuration
     * @param TestData|null $testData
     */
    public function __construct($configuration = null, $testData = null)
    {
        assert($configuration instanceof TestConfiguration || is_null($configuration));
        assert($testData instanceof TestData || is_null($testData));

        $this->setConfiguration($configuration);
        $this->initialize($testData);
        $this->invokeTestSuite();
    }


    /**
     * @param TestData|null $testData
     *
     * @return void
     */
    protected function initialize($testData = null)
    {
        $this->setTestData($testData);
    }

    /**
     * @param TestFactory $test
     *
     * @return void
     */
    protected function addTest($test)
    {
        assert($test instanceof TestFactory);
        $this->tests[] = $test;
    }

    /**
     * @return array
     */
    public function getTests()
    {
        assert(is_array($this->tests));
        return $this->tests;
    }

    /**
     * @return void
     */
    protected function calculateState()
    {
        $tests = $this->getTests();

        if (!empty($tests)) {
            $overall = array();
            foreach ($tests as $test) {
                $overall[] = $test->getState();
            }
            $this->setState(min($overall));
        }
    }

    /**
     * @return void
     */
    abstract protected function invokeTestSuite();
}
