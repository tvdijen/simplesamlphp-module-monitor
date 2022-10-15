<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor\Test;

use SimpleSAML\Module\monitor\DependencyInjection;

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
