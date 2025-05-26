<?php

declare(strict_types=1);

namespace SimpleSAML\Module\monitor;

use SimpleSAML\Assert\Assert;
use SimpleSAML\Utils;

use function array_key_exists;
use function array_merge;
use function is_null;

final class TestData
{
    /** @var array */
    private array $testData = [];


    /**
     * @param array $input
     */
    public function __construct(array $input = [])
    {
        $this->setInput($input);
    }


    /**
     * @param mixed|null $input
     * @param string|null $key
     *
     * @return void
     */
    public function setInput($input, ?string $key = null): void
    {
        if (is_null($key)) {
            Assert::isArray($input);

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
    public function addInput(string $key, mixed $value = null): void
    {
        if (isset($this->testData[$key])) {
            Assert::isArray($this->testData[$key]);

            $arrayUtils = new Utils\Arrays();
            $this->testData[$key] = array_merge($this->testData[$key], $arrayUtils->arrayize($value));
        } else {
            $this->testData[$key] = $value;
        }
    }


    /**
     * @return array
     */
    public function getInput(): array
    {
        return $this->testData;
    }


    /**
     * @param string $item
     *
     * @return mixed|null
     */
    public function getInputItem(string $item)
    {
        return array_key_exists($item, $this->testData) ? $this->testData[$item] : null;
    }
}
