<?php

namespace Dgame\Functional\Option;

use Dgame\Functional\ADT\BinaryADTFilterTrait;
use Dgame\Functional\ADT\BinaryADTInterface;
use Dgame\Functional\ADT\BinaryADTMapTrait;
use Dgame\Functional\ADT\BinaryADTTrait;
use Dgame\Functional\ADT\ADTFilterInterface;
use Dgame\Functional\ADT\ADTMapInterface;
use Dgame\Functional\Result\Err;
use Dgame\Functional\Result\Ok;
use Dgame\Functional\Result\Result;

/**
 * Class Option
 * @package Dgame\Functional\Option
 */
abstract class Option implements BinaryADTInterface, ADTFilterInterface, ADTMapInterface
{
    use BinaryADTFilterTrait;
    use BinaryADTMapTrait;
    use BinaryADTTrait;

    /**
     * @param $default
     *
     * @return Result
     */
    public function okOr($default): Result
    {
        return $this->isSome() ? new Ok(...$this->values) : new Err($default);
    }

    /**
     * @param callable $default
     *
     * @return Result
     */
    public function okOrElse(callable $default): Result
    {
        return $this->isSome() ? new Ok(...$this->values) : new Err($default());
    }

    /**
     * @param $value
     *
     * @return Option
     */
    public static function maybe($value): self
    {
        return $value === null ? new None() : new Some($value);
    }

    /**
     * @return bool
     */
    abstract public function isSome(): bool;

    /**
     * @return bool
     */
    public function isNone(): bool
    {
        return !$this->isSome();
    }

    /**
     * @return None
     */
    final protected function getCounterpart(): None
    {
        return new None();
    }

    /**
     * @return bool
     */
    final protected function isCounterpart(): bool
    {
        return $this->isNone();
    }
}
