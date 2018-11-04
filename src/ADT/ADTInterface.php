<?php

namespace Dgame\Functional\ADT;

/**
 * Interface ADTInterface
 * @package Dgame\Functional\ADT
 */
interface ADTInterface extends MatchInterface, DestructInterface
{
    /**
     * @return mixed
     */
    public function unwrap();

    /**
     * @param $default
     *
     * @return mixed
     */
    public function unwrapOr($default);

    /**
     * @param callable $default
     *
     * @return mixed
     */
    public function unwrapOrElse(callable $default);

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function expect(string $message);
}
