<?php

use \SimpleSAML\Module;

/**
 * Hook to add the monitor module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 * @return void
 */
function monitor_hook_frontpage(array &$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));
    $links['config'][] = [
        'href' => Module::getModuleURL('monitor/index.php'),
        'text' => ['en' => 'Monitor'],
    ];
}
