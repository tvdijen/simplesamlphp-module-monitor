<?php

declare(strict_types=1);

use SimpleSAML\Module;
use Webmozart\Assert\Assert;

/**
 * Hook to add the monitor module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function monitor_hook_frontpage(array &$links)
{
    Assert::keyExists($links, 'links');

    $links['config'][] = [
        'href' => Module::getModuleURL('monitor/index.php'),
        'text' => ['en' => 'Monitor'],
    ];
}
