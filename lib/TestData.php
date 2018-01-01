<?php

namespace SimpleSAML\Module\monitor;

final class TestData
{
    /**
     * @var array
     */
    protected $testData = array();

    /**
     * @param array $input
     */
    public function __construct($input = array())
    {
        assert(is_array($input));
        $this->setInput($input);
    }

    /**
     * @param mixed|null $input
     * @param string|null $key
     *
     * @return void
     */
    public function setInput($input, $key = null)
    {
        assert(is_string($key) || is_null($key));
        if (is_null($key)) {
            assert(is_array($input));
            foreach ($input as $key => $value) {
                $this->addInput($key, $value);
            }
        } elseif (array_key_exists($key, $this->testData)) {
            $this->testData[$key] = $input;
        } else {
            $this->addInput($key, $input);
        }
    }

    /**
     * @param string $key
     * @param mixed|null $value
     *
     * @return void
     */
    public function addInput($key, $value = null)
    {
        assert(is_string($key));
        if (isSet($this->testData[$key])) {
            assert(is_array($this->testData[$key]));
            $this->testData[$key] = array_merge($this->testData[$key], $value);
        } else {
            $this->testData[$key] = $value;
        }
    }

    /**
     * @param string|null $item
     *
     * @return mixed
     */
    public function getInput($item = null)
    {
        assert(is_string($item) || is_null($item));
        return is_null($item) ? $this->testData : (isSet($this->testData[$item]) ? $this->testData[$item] : null);
    }
}
