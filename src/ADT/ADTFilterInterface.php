<?php

namespace Dgame\Functional\ADT;

/**
 * Interface ADTFilterInterface
 * @package Dgame\Functional\ADT
 */
interface ADTFilterInterface
{
    /**
     * @param callable $predicate
     *
     * @return mixed
     */
    public function filter(callable $predicate);
}
