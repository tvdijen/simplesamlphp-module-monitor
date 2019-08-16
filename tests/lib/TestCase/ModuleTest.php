<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\TestCase as TestCase;
use \SimpleSAML\Modules\Monitor\TestData as TestData;
use \SimpleSAML\Modules\Monitor\State as State;

/**
 * Tests for TestCase\Module
 */
class TestModuleTest extends \PHPUnit\Framework\TestCase
{
    public function testModuleAvailable()
    {
        $testData = new TestData([
            'required' => 'mod_test',
            'available' => ['mod_test'],
            'type' => 'testtype',
        ]);
        $moduleTest = new TestCase\Module($testData);
        $testResult = $moduleTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
        $this->assertEquals('mod_test', $moduleTest->getModuleName());
    }

    public function testEitherModuleAvailable()
    {
        $testData = new TestData([
            'required' => 'mod_test|mod_test2',
            'available' => ['mod_test'],
            'type' => 'testtype',
        ]);
        $moduleTest = new TestCase\Module($testData);
        $testResult = $moduleTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
        $this->assertEquals('mod_test', $moduleTest->getModuleName());
    }

    public function testBothModulesAvailable()
    {
        $testData = new TestData([
            'required' => 'mod_test|mod_test2',
            'type' => 'testtype',
            'available' => ['mod_test', 'mod_test2'],
        ]);
        $moduleTest = new TestCase\Module($testData);
        $testResult = $moduleTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
        $this->assertEquals('mod_test', $moduleTest->getModuleName());
    }

    public function testModuleUnavailable()
    {
        $testData = new TestData([
            'required' => 'mod_test',
            'available' => [],
            'type' => 'testtype',
        ]);
        $moduleTest = new TestCase\Module($testData);
        $testResult = $moduleTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
        $this->assertEquals('mod_test', $moduleTest->getModuleName());
    }

    public function testBothModulesUnavailable()
    {
        $testData = new TestData([
            'required' => 'mod_test|mod_test2',
            'available' => [],
            'type' => 'testtype',
        ]);
        $moduleTest = new TestCase\Module($testData);
        $testResult = $moduleTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
        $this->assertEquals('mod_test|mod_test2', $moduleTest->getModuleName());
    }
}
