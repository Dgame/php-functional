<?php

namespace Dgame\Functional\ADT;

/**
 * Trait ADTTrait
 * @package Dgame\Functional\ADT
 */
trait ADTTrait
{
    use MatchTrait;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s(%s)', static::class, implode(', ', $this->values));
    }
}

