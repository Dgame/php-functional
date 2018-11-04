<?php

namespace Dgame\Functional\Result;

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
