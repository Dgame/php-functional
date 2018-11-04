<?php

namespace Dgame\Functional\Result;

use AssertionError;
use Dgame\Functional\ADT\ADTMapInterface;
use Dgame\Functional\ADT\BinaryADTInterface;
use Dgame\Functional\ADT\BinaryADTMapTrait;
use Dgame\Functional\ADT\BinaryADTTrait;

/**
 * Class Result
 * @package Dgame\Functional\Result
 */
abstract class Result implements BinaryADTInterface, ADTMapInterface
{
    use BinaryADTMapTrait;
    use BinaryADTTrait;

    /**
     * @return bool
     */
    abstract public function isOk(): bool;

    /**
     * @return mixed
     */
    public function unwrapErr()
    {
        if ($this->isOk()) {
            throw new AssertionError($this->unwrap());
        }

        return reset($this->values);
    }

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function expectErr(string $message)
    {
        if ($this->isOk()) {
            throw new AssertionError($message . ': ' . $this->unwrap());
        }

        return reset($this->values);
    }

    /**
     * @param callable $closure
     *
     * @return Result
     */
    public function mapErr(callable $closure): self
    {
        return $this->isOk() ? $this : new Err($closure(...$this->values));
    }

    /**
     * @return bool
     */
    public function isErr(): bool
    {
        return !$this->isOk();
    }

    /**
     * @return Err
     */
    final protected function getCounterpart(): Err
    {
        return new Err('Failure!');
    }

    /**
     * @return bool
     */
    final protected function isCounterpart(): bool
    {
        return $this->isErr();
    }
}
