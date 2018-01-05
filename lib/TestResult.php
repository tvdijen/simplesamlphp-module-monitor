<?php

namespace SimpleSAML\Module\monitor;

final class TestResult
{
    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $message;

    /**
     * @var State
     */
    private $state = State::NOSTATE;

    /**
     * @param string $category
     * @param string $subject
     */
    public function __construct($category = 'Unknown category', $subject = 'Unknown subject')
    {
        $this->category = $category;
        $this->subject = $subject;
    }

    /**
     * @param State $state
     *
     * @return void
     */
    public function setState($state = State::NOSTATE)
    {
        assert($state instanceof State);
        $this->state = $state;
    }

    /**
     * @return State
     */
    public function getState()
    {
        assert($this->state instanceof State);
        return $this->state;
    }
}
