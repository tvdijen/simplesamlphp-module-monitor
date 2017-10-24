<?php

use sspmod_monitor_State as State;

final class sspmod_monitor_TestCase_FileSystem_FreeSpace extends sspmod_monitor_TestCase
{
    private $path = null;

    protected function initialize()
    {
        $this->setPath($this->getInput('path'));
    }

    private function setPath($path)
    {
        assert(is_string($path));
        $this->path = $path;
    }

    private function getPath()
    {
        assert(is_string($this->path));
        return $this->path;
    }

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
