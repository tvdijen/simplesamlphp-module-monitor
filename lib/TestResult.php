<?php

namespace SimpleSAML\Module\monitor;

final class TestResult
{
    /** @var int The state reflecting the result */
    private $state = State::NOSTATE;

    /** @var string Test category this test belongs to */
    private $category;

    /** @var string The subject that was tested */
    private $subject;

    /** @var string Message describing the result */
    private $message = '';

    /** @var array Data to be used by TestSuite or other TestCases */
    private $output = [];


    /**
     * @param string $category
     * @param string $subject
     */
    public function __construct(string $category = 'Unknown category', string $subject = 'Unknown subject')
    {
        $this->setCategory($category);
        $this->setSubject($subject);
    }


    /**
     * @param bool $includeOutput
     *
     * @return array
     */
    public function arrayizeTestResult(bool $includeOutput = false): array
    {
        $output = [
            'state' => $this->getState(),
            'category' => $this->getCategory(),
            'subject' => $this->getSubject(),
            'message' => $this->getMessage()
        ];
        if ($includeOutput === true) {
            $output['output'] =  $this->getOutput();
        }
        return $output;
    }


    /**
     * @param string $subject
     *
     * @return void
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }


    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }


    /**
     * @param string $category
     *
     * @return void
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }


    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }


    /**
     * @param string $message
     *
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }


    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


    /**
     * @param array $value
     *
     * @return void
     */
    public function setOutput(array $value): void
    {
        $this->output = $value;
    }


    /**
     * @param mixed $value
     * @param string|null $index
     *
     * @return void
     */
    public function addOutput($value, string $index = null): void
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
    public function getOutput(string $key = null)
    {
        return is_null($key) ? $this->output : (isset($this->output[$key]) ? $this->output[$key] : null);
    }


    /**
     * @param integer $state
     *
     * @return void
     */
    public function setState(int $state = State::NOSTATE): void
    {
        $this->state = $state;
    }


    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }
}
