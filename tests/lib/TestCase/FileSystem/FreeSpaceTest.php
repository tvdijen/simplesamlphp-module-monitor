<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\State;

/**
 * Tests for TestCase\FileSystem\FreeSpace
 */
class TestFreeSpaceTest extends \PHPUnit\Framework\TestCase
{
    public function testFreeSpaceAvailable(): void
    {
        // /tmp/testdisk will be 1MB
        $testData = new TestData([
            'path' => '/tmp/testdisk',
            'category' => 'Test disk',
        ]);
        $spaceTest = new TestCase\FileSystem\FreeSpace($testData);
        $testResult = $spaceTest->getTestResult();
        $freePercentage = $testResult->getOutput('free_percentage');

        $this->assertGreaterThanOrEqual(15, $freePercentage);
        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testFreeSpaceAlmostOut(): void
    {
        // Fill /tmp/testdisk for 90%
        $free = (disk_free_space('/tmp/testdisk') / 100) * 90;
        file_put_contents('/tmp/testdisk/90percent.txt', str_repeat('a', (int)$free));

        $testData = new TestData([
            'path' => '/tmp/testdisk',
            'category' => 'Test disk',
        ]);
        $spaceTest = new TestCase\FileSystem\FreeSpace($testData);
        $testResult = $spaceTest->getTestResult();
        $freePercentage = $testResult->getOutput('free_percentage');

        $this->assertGreaterThanOrEqual(5.0, $freePercentage);
        $this->assertLessThan(15.0, $freePercentage);
        $this->assertEquals(State::WARNING, $testResult->getState());

        unlink('/tmp/testdisk/90percent.txt');
    }

    public function testFreeSpaceOut(): void
    {
        // Fill /tmp/testdisk for 99%
        $free = (disk_free_space('/tmp/testdisk') / 100) * 99;
        file_put_contents('/tmp/testdisk/99percent.txt', str_repeat('b', (int)$free));

        $testData = new TestData([
            'path' => '/tmp/testdisk',
            'category' => 'Test disk',
        ]);
        $spaceTest = new TestCase\FileSystem\FreeSpace($testData);
        $testResult = $spaceTest->getTestResult();
        $freePercentage = $testResult->getOutput('free_percentage');

        $this->assertLessThan(5.0, $freePercentage);
        $this->assertEquals(State::ERROR, $testResult->getState());

        unlink('/tmp/testdisk/99percent.txt');
    }
}
