<?php

namespace SimpleSAML\Module\monitor\TestCase\FileSystem;

use SimpleSAML\Module\monitor\State;
use SimpleSAML\Module\monitor\TestData;
use SimpleSAML\Module\monitor\TestResult;

final class FreeSpace extends \SimpleSAML\Module\monitor\TestCaseFactory
{
    /** @var string */
    private $path = '';


    /**
     * @var \SimpleSAML\Module\monitor\TestData $testData
     *
     * @return void
     */
    protected function initialize(TestData $testData): void
    {
        $this->setPath($testData->getInputItem('path'));
        $this->setCategory($testData->getInputItem('category'));
        parent::initialize($testData);
    }


    /**
     * @param string $path
     * @return void
     */
    private function setPath(string $path): void
    {
        $this->path = $path;
    }


    /**
     * @return string
     */
    private function getPath(): string
    {
        return $this->path;
    }


    /**
     * @return void
     */
    public function invokeTest(): void
    {
        $path = $this->getPath();
        $testResult = new TestResult($this->getCategory(), $path);

        $size = disk_total_space($path);
        $free = disk_free_space($path);
        if ($size !== false && $free !== false) {
            $free = round(100 - ((($size - $free) / $size) * 100));

            if ($free >= 15) {
                $testResult->setMessage($free . '% free space');
                $testResult->setState(State::OK);
            } elseif ($free < 5) {
                $testResult->setMessage('Critical: ' . $free . '% free space');
                $testResult->setState(State::ERROR);
            } else {
                $testResult->setMessage($free . '% free space');
                $testResult->setState(State::WARNING);
            }
            $testResult->addOutput($free, 'free_percentage');
        } else {
            $testResult->setMessage('Error collecting disk usage');
            $testResult->setState(State::FATAL);
        }
        $this->setTestResult($testResult);
    }
}
