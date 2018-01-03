<?php

namespace SimpleSAML\Module\monitor;

abstract class TestFactory
{
    /**
     * @var TestData|null
     */
    private $testData = null;

    /**
     * @var TestConfiguration|null
     */
    private $configuration = null;

    /**
     * @var array|null
     */
    private $output = array();

    /**
     * @var State
     */
    private $state = State::NOSTATE;

    /**
     * @var array
     */
    private $messages = array();


    /**
     * @param TestConfiguration|null $configuration
     *
     * @return void
     */
    protected function setConfiguration($configuration = null)
    {
        assert($configuration instanceof TestConfiguration);
        if (!is_null($configuration)) {
            $this->configuration = $configuration;
        }
    }


    /**
     * @return TestConfiguration
     */
    public function getConfiguration()
    {
        assert($this->configuration instanceof TestConfiguration);
        return $this->configuration;
    }


    /**
     * @return TestData|null
     */
    public function getTestData()
    {
        assert($this->testData instanceof TestData || is_null($this->testData));
        return $this->testData;
    }


    /**
     * @param TestData|null $testData
     *
     * @return void
     */
    protected function setTestData($testData = null)
    {
        assert($testData instanceof TestData || is_null($testData));
        if (!is_null($testData)) {
            $this->testData = $testData;
        }
    }


    /**
     * @return array
     */
    public function getMessages()
    {
        assert(is_array($this->messages));
        return $this->messages;
    }


    /**
     * @param string|null $item
     *
     * @return mixed
     */
    public function getOutput($item = null)
    {
        assert(is_string($item) || is_null($item));
        return is_null($item) ? $this->output : (isSet($this->output[$item]) ? $this->output[$item] : null);
    }


    /**
     * @return State
     */
    public function getState()
    {
        assert($this->state instanceof State);
        return $this->state;
    }


    /**
     * @return void
     */
    protected function setMessages($messages)
    {
        assert(is_array($messages));
        $this->messages = $messages;
    }


    /**
     * @param array $messages
     * @param string|null $index
     *
     * @return void
     */
    protected function addMessages($messages, $index = null)
    {
        if ($index === null) {
            $this->messages = array_merge($this->messages, $messages);
        } else {
            foreach ($messages as $message) {
                $this->messages[$index][] = $message;
            }
        }
    }


    /**
     * @param State $state
     * @param string $category
     * @param string $subject
     * @param string $message
     *
     * @return void
     */
    protected function addMessage($state, $category, $subject, $message)
    {
        assert(($state instanceof State) && is_string($category) && is_string($subject) && is_string($message));
        $this->messages[] = array($state, $category, $subject, $message);
    }


    /**
     * @param mixed $value
     * @param string|null $index
     *
     * @return void
     */
    protected function addOutput($value, $index = null)
    {
        if ($index === null) {
            $this->output = array_merge($this->output, $value);
        } else {
            $this->output[$index] = $value;
        }
    }


    /**
     * @return void
     */
    protected function setOutput($output)
    {
        assert(is_array($output));
        $this->output = $output;
    }


    /**
     * @return void
     */
    protected function setState($state)
    {
        assert($state instanceof State);
        $this->state = $state;
    }
}
