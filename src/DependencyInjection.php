<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor;

use function array_key_exists;

final class DependencyInjection
{
    /** @var array<mixed> */
    private $vars;


    /**
     * @param array<mixed> $vars
     */
    public function __construct(array $vars)
    {
        $this->vars = $vars;
    }


    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return array_key_exists($key, $this->vars) ? $this->vars[$key] : null;
    }
}
