<?php

use sspmod_monitor_State as State;

abstract class sspmod_monitor_Test
{
    private $input = array('category' => 'Unknown category');
    private $output = array();
    private $state = State::NOSTATE;
    private $messages = array();

    public function getMessages()
    {
        assert(is_array($this->messages));
        return $this->messages;
    }

    protected function getInput($item = null)
    {
        assert(is_string($item) || is_null($item));
        return is_null($item) ? $this->input : (isSet($this->input[$item]) ? $this->input[$item] : null);
    }

    public function getOutput($item = null)
    {
        assert(is_string($item) || is_null($item));
        return is_null($item) ? $this->output : (isSet($this->output[$item]) ? $this->output[$item] : null);
    }

    public function getState()
    {
        assert(is_int($this->state));
        return $this->state;
    }

    protected function setMessages($messages)
    {
        assert(is_array($messages));
        $this->messages = $messages;
    }

    protected function addMessages($messages, $index = null)
    {
        if ($index === null) {
            $this->messages = array_merge($this->messages, $messages);
        } else {
            $this->messages[$index] = $messages;
        }
    }

    protected function addMessage($state, $category, $subject, $message)
    {
        assert(is_int($state) && is_string($category) && is_string($subject) && is_string($message));
        $this->messages[] = array($state, $category, $subject, $message);
    }

    protected function setInput($input)
    {
        assert(is_array($input) || is_null($input));
        $this->input = $input;
    }

    protected function addOutput($value, $index = null)
    {
        if ($index === null) {
            $this->output = array_merge($this->output, $value);
        } else {
            $this->output[$index] = $value;
        }
    }

    protected function setOutput($output)
    {
        assert(is_array($output));
        $this->output = $output;
    }

    protected function setState($state)
    {
        assert(is_int($state));
        $this->state = $state;
    }
}
