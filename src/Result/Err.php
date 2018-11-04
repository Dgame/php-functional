<?php

namespace Dgame\Functional\Result;

use AssertionError;
use Dgame\Functional\ADT\DefaultUnwrapTrait;

/**
 * Class Err
 * @package Dgame\Functional\Result
 */
final class Err extends Result
{
    use DefaultUnwrapTrait;

    /**
     * Err constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->values = [$value];
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        return false;
    }

    /**
     * @return mixed|void
     */
    public function unwrap()
    {
        $this->expect('Unwraped Err');
    }

    /**
     * @param string $message
     *
     * @return mixed|void
     */
    public function expect(string $message)
    {
        throw new AssertionError($message . ': ' . reset($this->values));
    }
}
