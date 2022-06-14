<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\State;

/**
 * Tests for TestCase\Module
 */
class TestModuleTest extends \PHPUnit\Framework\TestCase
{
    public function testModuleAvailable(): void
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

    public function testEitherModuleAvailable(): void
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

    public function testBothModulesAvailable(): void
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

    public function testModuleUnavailable(): void
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

    public function testBothModulesUnavailable(): void
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
