<?php

namespace Dgame\Functional\ADT;

/**
 * Trait BinaryADTMapTrait
 * @package Dgame\Functional\ADT
 */
trait BinaryADTMapTrait
{
    use BinaryCounterpartTrait;

    /**
     * @param callable $closure
     *
     * @return BinaryADTMapTrait
     */
    public function map(callable $closure): self
    {
        if ($this->isCounterpart()) {
            return $this;
        }

        $result = $this->mapOr($closure, null);

        return $result === null ? $this->getCounterpart() : new static($result);
    }

    /**
     * @param callable $closure
     * @param          $default
     *
     * @return ADTTrait
     */
    public function mapOr(callable $closure, $default)
    {
        return $this->mapOrElse($closure, function () use ($default) {
            return $default;
        });
    }

    /**
     * @param callable $closure
     * @param callable $default
     *
     * @return mixed
     */
    public function mapOrElse(callable $closure, callable $default)
    {
        if ($this->isCounterpart() || empty($this->values)) {
            return $default();
        }

        return $closure(...$this->values);
    }
}
