<?php

namespace SimpleSAML\Module\monitor;

final class TestResult
{
    /**
     * @var int     The state reflecting the result
     */
    private $state;

    /**
     * @var string  Test category this test belongs to
     */
    private $category;

    /**
     * @var string  The subject that was tested
     */
    private $subject;

    /**
     * @var string  Message describing the result
     */
    private $message;

    /**
     * @var array   Data to be used by TestSuite or other TestCases
     */
    private $output;

    /**
     * @param string $category
     * @param string $subject
     */
    public function __construct($category = 'Unknown category', $subject = 'Unknown subject')
    {
        $this->setCategory($category);
        $this->setSubject($subject);
        $this->setOutput(array());
        $this->setState(State::NOSTATE);
    }

    /**
     * param bool $includeOutput
     *
     * @return array
     */
    public function arrayizeTestResult($includeOutput = false)
    {
        $output = [
            $this->getState(),
            $this->getCategory(),
            $this->getSubject(),
            $this->getMessage()
        ];
        if ($includeOutput === true) {
           $output[] =  $this->getOutput();
        }
        return $output;
    }

    /**
     * @param string $subject
     *
     * @return void
     */
    public function setSubject($subject)
    {
        assert(is_string($subject));
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        assert(is_string($this->subject));
        return $this->subject;
    }

    /**
     * @param string $category
     *
     * @return void
     */
    public function setCategory($category)
    {
        assert(is_string($category));
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        assert(is_string($this->category));
        return $this->category;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function setMessage($message)
    {
        assert(is_string($message));
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        assert(is_string($this->message));
        return $this->message;
    }

    /**
     * @param array $value
     *
     * @return void
     */
    public function setOutput($value)
    {
        assert(is_array($value));
        $this->output = $value;
    }

    /**
     * @param mixed $value
     * @param string|null $index
     *
     * @return void
     */
    public function addOutput($value, $index = null)
    {
        if ($index === null) {
            $this->output = array_merge($this->output, $value);
        } else {
            $this->output[$index] = $value;
        }
    }

    /**
     * @param string|null $key
     *
     * @return mixed
     */
    public function getOutput($key = null)
    {
        assert(is_array($this->output));
        return is_null($key) ? $this->output : (isSet($this->output[$key]) ? $this->output[$key] : null);
    }
    
    /**
     * @param int $state
     *
     * @return void
     */
    public function setState($state = State::NOSTATE)
    {
        assert(is_int($state));
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getState()
    {
        assert(is_int($this->state));
        return $this->state;
    }
}
