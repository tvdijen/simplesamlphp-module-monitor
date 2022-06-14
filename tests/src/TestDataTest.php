<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\TestData;

/**
 * Tests for TestData
 */
class TestDataTest extends \PHPUnit\Framework\TestCase
{
    public function testTestData(): void
    {
        $input = ['test' => [1, 2, 3]];
        $testData = new TestData($input);
        $this->assertEquals($input, $testData->getInput());

        $input['blub'] = [4, 5, 6];
        $testData->setInput(['blub' => [4, 5, 6]]);
        $this->assertEquals($input, $testData->getInput());

        $input['mehh'] = [7, 8, 9];
        $testData->setInput([7, 8, 9], 'mehh');
        $this->assertEquals($input, $testData->getInput());

        $testData->setInput([10], 'mehh');
        $input['mehh'] = [10];
        $this->assertEquals($input, $testData->getInput());

        $testData->addInput('mehh', [7, 8, 9]);
        $input['mehh'] = [10, 7, 8, 9];
        $this->assertEquals($input, $testData->getInput());

        $this->assertEquals($input['mehh'], $testData->getInputItem('mehh'));
    }
}
