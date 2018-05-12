<?php

namespace SimpleSAML\Module\monitor\Test;

use \SimpleSAML\Module\monitor\DependencyInjection as DependencyInjection;

/**
 * Tests for DependencyInjection
 */
class DependencyInjectionTest extends \PHPUnit_Framework_TestCase
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
