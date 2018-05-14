<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\TestCase as TestCase;
use \SimpleSAML\Module\monitor\TestData as TestData;
use \SimpleSAML\Module\monitor\State as State;

/**
 * Tests for TestCase\FileSystem\FreeSpace
 */
class TestFreeSpaceTest extends \PHPUnit_Framework_TestCase
{
    public function testFreeSpaceAvailable()
    {
        // /tmp/testdisk will be 1MB
        $testData = new TestData([
            'path' => '/tmp/testdisk',
            'category' => 'Test disk',
        ]);
        $spaceTest = new TestCase\FileSystem\FreeSpace($testData);
        $testResult = $spaceTest->getTestResult();
        $freePercentage = $testResult->getOutput('free_percentage');

        $this->assertEquals(93, $freePercentage);
        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testFreeSpaceAlmostOut()
    {
        // Fill /tmp/testdisk for 90%
        $free = (disk_free_space('/tmp/testdisk') / 100) * 90;
        file_put_contents('/tmp/testdisk/90filler.txt', str_repeat('a', (int)$free));

        $testData = new TestData([
            'path' => '/tmp/testdisk',
            'category' => 'Test disk',
        ]);
        $spaceTest = new TestCase\FileSystem\FreeSpace($testData);
        $testResult = $spaceTest->getTestResult();
        $freePercentage = $testResult->getOutput('free_percentage');

        $this->assertEquals(9.0, $freePercentage);
        $this->assertEquals(State::WARNING, $testResult->getState());
    }

    public function testFreeSpaceOut()
    {
        // Fill /tmp/testdisk for 95%
        $free = (disk_free_space('/tmp/testdisk') / 100) * 50;
        file_put_contents('/tmp/testdisk/95filler.txt', str_repeat('b', (int)$free));

        $testData = new TestData([
            'path' => '/tmp/testdisk',
            'category' => 'Test disk',
        ]);
        $spaceTest = new TestCase\FileSystem\FreeSpace($testData);
        $testResult = $spaceTest->getTestResult();
        $freePercentage = $testResult->getOutput('free_percentage');

        $this->assertEquals(4.0, $freePercentage);
        $this->assertEquals(State::ERROR, $testResult->getState());
    }
}
