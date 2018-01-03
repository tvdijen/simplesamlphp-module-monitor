<?php

namespace \SimpleSAML\Module\monitor;

abstract class DependencyInjectionFactory
{
    /**
     * @param array $vars
     *
     * @return Request
     */

    public function __construct($vars)
    {
        $objectVars = get_object_vars($this);
        foreach ($vars as $property => $value)
        {
            if (array_key_exists($property, $objectVars)) {
                $this->$property = $value;
            }
        }
        return $this;
    }
}
