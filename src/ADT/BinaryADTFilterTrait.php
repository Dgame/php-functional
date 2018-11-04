<?php

namespace Dgame\Functional\ADT;

/**
 * Trait BinaryADTFilterTrait
 * @package Dgame\Functional\ADT
 */
trait BinaryADTFilterTrait
{
    use BinaryCounterpartTrait;

    /**
     * @param callable $predicate
     *
     * @return BinaryADTFilterTrait
     */
    public function filter(callable $predicate): self
    {
        return $this->isCounterpart() ?
            $this : (empty($this->values) ?
                $this->getCounterpart() : ($predicate(...$this->values) ?
                    $this : $this->getCounterpart()));
    }
}
