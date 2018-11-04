<?php

namespace Dgame\Functional\ADT;

use AssertionError;

/**
 * Trait DefaultUnwrapTrait
 * @package Dgame\Functional\ADT
 */
trait DefaultUnwrapTrait
{
    /**
     * @param $default
     *
     * @return mixed
     */
    public function unwrapOr($default)
    {
        return $default;
    }

    /**
     * @param callable $default
     *
     * @return mixed
     */
    public function unwrapOrElse(callable $default)
    {
        return $default();
    }

    /**
     * @param string $message
     */
    public function expect(string $message): void
    {
        throw new AssertionError($message);
    }
}
