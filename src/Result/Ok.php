<?php

namespace Dgame\Functional\Result;

use Dgame\Functional\ADT\SafeUnwrapTrait;

/**
 * Class Ok
 * @package Dgame\Functional\Result
 */
final class Ok extends Result
{
    use SafeUnwrapTrait;

    /**
     * Ok constructor.
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
        return true;
    }

    /**
     * @return mixed
     */
    public function unwrap()
    {
        return reset($this->values);
    }
}
