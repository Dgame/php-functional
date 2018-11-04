<?php

namespace Dgame\Functional\ADT;

/**
 * Trait BinaryADTTrait
 * @package Dgame\Functional\ADT
 */
trait BinaryADTTrait
{
    use ADTTrait;
    use BinaryCounterpartTrait;

    /**
     * @param BinaryADTInterface $adt
     *
     * @return BinaryADTTrait
     */
    public function and(BinaryADTInterface $adt): self
    {
        return $this->isCounterpart() ? $this : $adt;
    }

    /**
     * @param callable $closure
     *
     * @return BinaryADTTrait
     */
    public function andThen(callable $closure): self
    {
        return $this->isCounterpart() ? $this : (empty($this->values) ? $this->getCounterpart() : $closure(...$this->values));
    }

    /**
     * @param BinaryADTInterface $adt
     *
     * @return BinaryADTTrait
     */
    public function or(BinaryADTInterface $adt): self
    {
        return $this->isCounterpart() ? $adt : $this;
    }

    /**
     * @param callable $closure
     *
     * @return BinaryADTTrait
     */
    public function orElse(callable $closure): self
    {
        return $this->isCounterpart() ? $closure() : $this;
    }
}
