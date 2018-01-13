<?php

namespace SimpleSAML\Module\monitor;

final class TestData
{
    /**
     * @var array
     */
    private $testData = array();

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
     * @return array
     */
    public function getInput()
    {
        return $this->testData;
    }

    /**
     * @param string $item
     *
     * @return mixed|null
     */
    public function getInputItem($item) {
        assert(is_string($item));
        return array_key_exists($item, $this->testData) ? $this->testData[$item] : null;
    }
}
