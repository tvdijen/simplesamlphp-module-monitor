<?php

final class sspmod_monitor_TestSuite_Store_Phpsession extends sspmod_monitor_TestSuite
{
    protected function invokeTestSuite()
    {
        $path = session_save_path();
        $test = new sspmod_monitor_TestCase_FileSystem_FreeSpace($this, array('path' => $path));
        $this->addTest($test);

        $this->setMessages($test->getMessages());
        parent::invokeTestSuite();
    }
}
