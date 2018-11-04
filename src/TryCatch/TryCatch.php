<?php

namespace Dgame\Functional\TryCatch;

use AssertionError;
use Dgame\Functional\Option\None;
use Dgame\Functional\Option\Option;
use Dgame\Functional\Option\Some;
use Dgame\Functional\Result\Err;
use Dgame\Functional\Result\Ok;
use Dgame\Functional\Result\Result;
use Throwable;

/**
 * Class TryCatch
 * @package Dgame\Functional\TryCatch
 */
final class TryCatch implements TryCatchInterface
{
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var Throwable
     */
    private $throwable;
    /**
     * @var bool
     */
    private $recovered = false;

    /**
     * TryCatch constructor.
     *
     * @param callable $closure
     * @param mixed    ...$args
     */
    public function __construct(callable $closure, ...$args)
    {
        try {
            $this->value = $closure(...$args);
        } catch (Throwable $throwable) {
            $this->throwable = $throwable;
        }
    }

    /**
     * @throws Throwable
     */
    public function __destruct()
    {
        if (!$this->recovered) {
            $this->throw();
        }
    }

    /**
     *
     */
    public function ignoreFailure(): void
    {
        $this->recovered = true;
    }

    /**
     * @throws Throwable
     */
    private function throw(): void
    {
        if ($this->isFailed()) {
            throw $this->throwable;
        }
    }

    /**
     * @param string $message
     */
    private function throwWith(string $message): void
    {
        if ($this->isFailed()) {
            throw new AssertionError($message, 0, $this->throwable);
        }
    }

    /**
     * @param null $value
     *
     * @return bool
     */
    public function isSuccess(&$value = null): bool
    {
        $value = $this->value;

        return !$this->isFailed();
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->throwable !== null;
    }

    /**
     * @return mixed
     * @throws Throwable
     */
    public function get()
    {
        $this->throw();

        return $this->value;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOr($default)
    {
        return $this->isSuccess($value) ? $value : $default;
    }

    /**
     * @param callable $closure
     *
     * @return mixed
     */
    public function getOrElse(callable $closure)
    {
        return $this->isSuccess($value) ? $value : $closure();
    }

    /**
     * @param string|null $message
     *
     * @return mixed
     * @throws Throwable
     */
    public function getOrThrow(string $message = null)
    {
        if (!empty($message)) {
            $this->throwWith($message);
        }

        return $this->get();
    }

    /**
     * @param callable $predicate
     *
     * @return TryCatchInterface
     */
    public function filter(callable $predicate): TryCatchInterface
    {
        if ($this->isSuccess($value) && !$predicate($value)) {
            $this->throwable = new AssertionError('Predicate does not match');
        }

        return $this;
    }

    /**
     * @param callable $closure
     *
     * @return TryCatchInterface
     */
    public function map(callable $closure): TryCatchInterface
    {
        if ($this->isSuccess($value)) {
            return new self($closure, $value);
        }

        return $this;
    }

    /**
     * @param callable $closure
     *
     * @return TryCatchInterface
     */
    public function recoverWith(callable $closure): TryCatchInterface
    {
        if ($this->isSuccess()) {
            return $this;
        }

        $try             = new self($closure, $this->throwable);
        $this->recovered = $try->isSuccess();

        return $try;
    }

    /**
     * @return Result
     */
    public function toResult(): Result
    {
        $this->ignoreFailure();

        if ($this->isSuccess($value)) {
            return new Ok($value);
        }

        return new Err($this->throwable);
    }

    /**
     * @return Option
     */
    public function toOption(): Option
    {
        $this->ignoreFailure();

        if ($this->isSuccess($value)) {
            return new Some($value);
        }

        return new None();
    }
}
