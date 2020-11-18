<?php

namespace SimpleSAML\Module\monitor;

final class DependencyInjection
{
    /** @var array */
    private $vars;


    /**
     * @param array $vars
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
