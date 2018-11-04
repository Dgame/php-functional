<?php

namespace Dgame\Functional\ADT;

/**
 * Interface ADTMapInterface
 * @package Dgame\Functional\ADT
 */
interface ADTMapInterface
{
    /**
     * @param callable $closure
     *
     * @return mixed
     */
    public function map(callable $closure);

    /**
     * @param callable $closure
     * @param          $default
     *
     * @return mixed
     */
    public function mapOr(callable $closure, $default);

    /**
     * @param callable $closure
     * @param callable $default
     *
     * @return mixed
     */
    public function mapOrElse(callable $closure, callable $default);
}
