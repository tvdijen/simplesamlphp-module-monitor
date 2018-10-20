<?php
/**
 * Hook to add the themejustitie module to the frontpage.
 *
 * @param array &$links  The links on the frontpage, split into sections.
 */

function monitor_hook_frontpage(array &$links)
{
    assert(is_array($links));
    assert(array_key_exists('links', $links));
    $links['config'][] = [
        'href' => \SimpleSAML\Module::getModuleURL('monitor/index.php'),
        'text' => ['en' => 'Monitor'],
    ];
}
