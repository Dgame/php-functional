<?php

namespace Dgame\Functional\ADT;

/**
 * Trait SafeUnwrapTrait
 * @package Dgame\Functional\ADT
 */
trait SafeUnwrapTrait
{
    /**
     * @param $default
     *
     * @return mixed
     */
    public function unwrapOr($default)
    {
        return $this->unwrap();
    }

    /**
     * @param callable $default
     *
     * @return mixed
     */
    public function unwrapOrElse(callable $default)
    {
        return $this->unwrap();
    }

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function expect(string $message)
    {
        return $this->unwrap();
    }
}
