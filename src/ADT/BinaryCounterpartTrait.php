<?php

namespace Dgame\Functional\ADT;

/**
 * Trait BinaryCounterpartTrait
 * @package Dgame\Functional\ADT
 */
trait BinaryCounterpartTrait
{
    /**
     * @return mixed
     */
    abstract protected function getCounterpart();

    /**
     * @return bool
     */
    abstract protected function isCounterpart(): bool;
}
