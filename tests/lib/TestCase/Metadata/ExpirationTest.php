<?php

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\TestCase;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\State;

/**
 * Tests for TestCase\Metadata\Expiration
 */
class TestMetadataExpirationTest extends \PHPUnit\Framework\TestCase
{
    public function testMetadataExpired(): void
    {
        $testData = new TestData([
            'entityId' => 'https://example.org',
            'entityMetadata' => ['expire' => time() - 1000],
        ]);
        $expirationTest = new TestCase\Metadata\Expiration($testData);
        $testResult = $expirationTest->getTestResult();

        $this->assertEquals(State::ERROR, $testResult->getState());
    }

    public function testMetadataValid(): void
    {
        $testData = new TestData([
            'entityId' => 'https://example.org',
            'entityMetadata' => ['expire' => time() + 1000],
        ]);
        $expirationTest = new TestCase\Metadata\Expiration($testData);
        $testResult = $expirationTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
    }

    public function testMetadataNeverExpires(): void
    {
        $testData = new TestData([
            'entityId' => 'https://example.org',
            'entityMetadata' => [],
        ]);
        $expirationTest = new TestCase\Metadata\Expiration($testData);
        $testResult = $expirationTest->getTestResult();

        $this->assertEquals(State::OK, $testResult->getState());
    }
}
