<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\TestData as TestData;

/**
 * Tests for TestData
 */
class TestDataTest extends \PHPUnit_Framework_TestCase
{
    public function testTestData()
    {
        $input = array('test' => array(1, 2, 3));
        $testData = new TestData($input);
        $this->assertEquals($input, $testData->getInput());

        $input['blub'] = array(4, 5, 6);
        $testData->setInput(array('blub' => array(4, 5, 6)));
        $this->assertEquals($input, $testData->getInput());

        $input['mehh'] = array(7, 8, 9);
        $testData->setInput(array(7, 8, 9), 'mehh');
        $this->assertEquals($input, $testData->getInput());

        $testData->setInput(array(10), 'mehh');
        $input['mehh'] = array(10);
        $this->assertEquals($input, $testData->getInput());

        $testData->addInput('mehh', array(7, 8, 9));
        $input['mehh'] = array(10, 7, 8, 9);
        $this->assertEquals($input, $testData->getInput());

        $this->assertEquals($input['mehh'], $testData->getInputItem('mehh'));
    }
}
