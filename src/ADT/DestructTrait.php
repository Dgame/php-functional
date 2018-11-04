<?php

namespace Dgame\Functional\ADT;

/**
 * Trait DestructTrait
 * @package Dgame\Functional\ADT
 */
trait DestructTrait
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @return array
     */
    public function destruct(): array
    {
        return $this->values;
    }
}
