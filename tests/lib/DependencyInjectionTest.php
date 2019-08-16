<?php

namespace SimpleSAML\Modules\Monitor\Test;

use \SimpleSAML\Modules\Monitor\DependencyInjection as DependencyInjection;

/**
 * Tests for DependencyInjection
 */
class DependencyInjectionTest extends \PHPUnit\Framework\TestCase
{
    public function testIO()
    {
        $variables = [
            'test' => 'test',
        ];
        $di = new DependencyInjection($variables);

        $this->assertEquals('test', $di->get('test'));
    }
}
