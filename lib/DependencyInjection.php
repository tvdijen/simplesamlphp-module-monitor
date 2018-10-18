<?php

namespace SimpleSAML\Modules\Monitor;

final class DependencyInjection
{
    /**
     * @var array
     */
    private $vars;

    /**
     * @param array $vars
     */
    public function __construct($vars)
    {
        $this->vars = $vars;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return array_key_exists($key, $this->vars) ? $this->vars[$key] : null;
    }
}
