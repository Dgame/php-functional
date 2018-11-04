<?php

namespace Dgame\Functional\TryCatch;

use Dgame\Functional\Option\Option;
use Dgame\Functional\Result\Result;

/**
 * Interface TryCatchInterface
 * @package Dgame\Functional\TryCatch
 */
interface TryCatchInterface
{
    /**
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * @return bool
     */
    public function isFailed(): bool;

    /**
     *
     */
    public function ignoreFailure(): void;

    /**
     * @return mixed
     */
    public function unwrap();

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function unwrapOr($default);

    /**
     * @param callable $closure
     *
     * @return mixed
     */
    public function unwrapOrElse(callable $closure);

    /**
     * @param string|null $message
     *
     * @return mixed
     */
    public function unwrapOrThrow(string $message = null);

    /**
     * @param callable $predicate
     *
     * @return TryCatchInterface
     */
    public function filter(callable $predicate): self;

    /**
     * @param callable $closure
     *
     * @return TryCatchInterface
     */
    public function map(callable $closure): self;

    /**
     * @param callable $closure
     *
     * @return TryCatchInterface
     */
    public function recoverWith(callable $closure): self;

    /**
     * @return Result
     */
    public function toResult(): Result;

    /**
     * @return Option
     */
    public function toOption(): Option;
}
