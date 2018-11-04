<?php

namespace Dgame\Functional\ADT;

/**
 * Interface BinaryADTInterface
 * @package Dgame\Functional\ADT
 */
interface BinaryADTInterface extends ADTInterface
{
    /**
     * @param self $adt
     *
     * @return mixed
     */
    public function and(self $adt);

    /**
     * @param callable $closure
     *
     * @return mixed
     */
    public function andThen(callable $closure);

    /**
     * @param self $adt
     *
     * @return mixed
     */
    public function or(self $adt);

    /**
     * @param callable $closure
     *
     * @return mixed
     */
    public function orElse(callable $closure);
}
