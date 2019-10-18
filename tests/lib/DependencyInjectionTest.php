<?php

namespace SimpleSAML\Module\Monitor\Test;

use SimpleSAML\Module\Monitor\DependencyInjection;

/**
 * Tests for DependencyInjection
 */
class DependencyInjectionTest extends \PHPUnit\Framework\TestCase
{
    public function testIO(): void
    {
        $variables = [
            'test' => 'test',
        ];
        $di = new DependencyInjection($variables);

        $this->assertEquals('test', $di->get('test'));
    }
}
