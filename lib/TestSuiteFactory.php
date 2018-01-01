<?php

namespace SimpleSAML\Module\monitor;

abstract class TestSuiteFactory extends TestFactory
{
    private $configuration = null;
    private $tests = null;

    /**
     * @param Configuration $configuration
     */
    public function __construct($configuration, $input)
    {
        assert($configuration instanceof Configuration);
        assert(is_array($input));

        $this->setConfiguration($configuration);
        $this->setInput($input);
        $this->initialize();
        $this->setInput(null);
        $this->invokeTestSuite();
    }

    /*
     * @return void
     */
    abstract protected function initialize();

    /*
     * @param Configuration $configuration
     *
     * @return void
     */
    private function setConfiguration($configuration)
    {
        assert($configuration instanceof Configuration);
        $this->configuration = $configuration;
    }

    /*
     * @return Configuration
     */
    public function getConfiguration()
    {
        assert($configuration instanceof Configuration);
        return $this->configuration;
    }

    /*
     * @return void
     */
    protected function addTest($test)
    {
        assert($test instanceof TestFactory);
        $this->tests[] = $test;
    }

    /*
     * @return array
     */
    public function getTests()
    {
        assert(is_array($this->tests));
        return $this->tests;
    }

    /*
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

    /*
     * @return void
     */
    abstract protected function invokeTestSuite();
}
