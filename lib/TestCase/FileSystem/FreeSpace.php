<?php

namespace SimpleSAML\Module\monitor\TestCase\FileSystem;

use \SimpleSAML\Module\monitor\State as State;
use \SimpleSAML\Module\monitor\TestData as TestData;

final class FreeSpace extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /**
     * @var string|null
     */
    private $path = null;

    /**
     * @var TestData $testData
     *
     * @return void
     */
    protected function initialize($testData)
    {
        $this->setPath($testData->getInput('path'));
        parent::initialize($testData);
    }

    /**
     * @return void
     */
    private function setPath($path)
    {
        assert(is_string($path));
        $this->path = $path;
    }

    /**
     * @return string
     */
    private function getPath()
    {
        assert(is_string($this->path));
        return $this->path;
    }

    /**
     * @return void
     */
    protected function invokeTest()
    {
        $path = $this->getPath();

        $size = disk_total_space($path);
        $used = $size - disk_free_space($path);
        $free = round(100 - (($used / $size) * 100));
        $this->addOutput($free, 'free_percentage');

        if ($free >= 15) {
            $this->addMessage(State::OK, 'Session storage', $path, $free . '% free space');
            $this->setState(State::OK);
        } else if ($free < 5) {
            $this->addMessage(State::ERROR, 'Session storage', $path, 'Critical: ' . $free . '% free space');
            $this->setState(State::ERROR);
        } else {
            $this->addMessage(State::WARNING, 'Session storage', $path, $free . '% free space');
            $this->setState(State::WARNING);
        }
    }
}
