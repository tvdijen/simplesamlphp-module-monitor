<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestResult as TestResult;

/**
 * Tests for DependencyInjection
 */
class TestResultTest extends \PHPUnit_Framework_TestCase
{
    public function testTestResult()
    {
        $testResult = new TestResult();

        $this->assertEquals('Unknown category', $testResult->getCategory());
        $this->assertEquals('Unknown subject', $testResult->getSubject());
        $this->assertEquals(array(), $testResult->getOutput());
        $this->assertEquals(State::NOSTATE, $testResult->getState());
        $this->assertEquals('', $testResult->getMessage());

        $testResult->setCategory('aaa');
        $testResult->setSubject('zzz');
        $testResult->setState(State::OK);
        $testResult->setMessage('test');
        $testResult->addOutput(array(10));

        $this->assertEquals('aaa', $testResult->getCategory());
        $this->assertEquals('zzz', $testResult->getSubject());
        $this->assertEquals(State::OK, $testResult->getState());
        $this->assertEquals('test', $testResult->getMessage());
        $this->assertEquals(array(10), $testResult->getOutput());

        $testResult->setOutput(array('test' => array(99)));
        $this->assertEquals(array('test' => array(99)), $testResult->getOutput());

        $testResult->addOutput(array(10), 'test');
        $this->assertEquals(array('test' => array(10)), $testResult->getOutput());

        $testResult->addOutput(array(99), 'testing');
        $this->assertEquals(array('test' => array(10), 'testing' => array(99)), $testResult->getOutput());

        $output = $testResult->arrayizeTestResult();
        $expected = array(
            'category' => 'aaa',
            'subject' => 'zzz',
            'state' => State::OK,
            'message' => 'test',
        );
        $this->assertEquals($expected, $output);

        $output = $testResult->arrayizeTestResult(true);
        $expected['output'] = array('test' => array(10), 'testing' => array(99));
        $this->assertEquals($expected, $output);
    }
}
