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
     * @param mixed|null $value
     *
     * @return bool
     */
    public function isSuccess(&$value = null): bool;

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
    public function get();

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOr($default);

    /**
     * @param callable $closure
     *
     * @return mixed
     */
    public function getOrElse(callable $closure);

    /**
     * @param string|null $message
     *
     * @return mixed
     */
    public function getOrThrow(string $message = null);

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
