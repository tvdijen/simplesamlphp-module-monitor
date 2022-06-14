<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestResult;

/**
 * Tests for DependencyInjection
 */
class TestResultTest extends \PHPUnit\Framework\TestCase
{
    public function testTestResult(): void
    {
        $testResult = new TestResult();

        $this->assertEquals('Unknown category', $testResult->getCategory());
        $this->assertEquals('Unknown subject', $testResult->getSubject());
        $this->assertEquals([], $testResult->getOutput());
        $this->assertEquals(State::NOSTATE, $testResult->getState());
        $this->assertEquals('', $testResult->getMessage());

        $testResult->setCategory('aaa');
        $testResult->setSubject('zzz');
        $testResult->setState(State::OK);
        $testResult->setMessage('test');
        $testResult->addOutput([10]);

        $this->assertEquals('aaa', $testResult->getCategory());
        $this->assertEquals('zzz', $testResult->getSubject());
        $this->assertEquals(State::OK, $testResult->getState());
        $this->assertEquals('test', $testResult->getMessage());
        $this->assertEquals([10], $testResult->getOutput());

        $testResult->setOutput(['test' => [99]]);
        $this->assertEquals(['test' => [99]], $testResult->getOutput());

        $testResult->addOutput([10], 'test');
        $this->assertEquals(['test' => [10]], $testResult->getOutput());

        $testResult->addOutput([99], 'testing');
        $this->assertEquals(['test' => [10], 'testing' => [99]], $testResult->getOutput());

        $output = $testResult->arrayizeTestResult();
        $expected = [
            'category' => 'aaa',
            'subject' => 'zzz',
            'state' => State::OK,
            'message' => 'test',
        ];
        $this->assertEquals($expected, $output);

        $output = $testResult->arrayizeTestResult(true);
        $expected['output'] = ['test' => [10], 'testing' => [99]];
        $this->assertEquals($expected, $output);
    }
}
