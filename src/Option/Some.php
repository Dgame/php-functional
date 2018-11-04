<?php

namespace Dgame\Functional\Option;

use Dgame\Functional\ADT\SafeUnwrapTrait;

/**
 * Class Some
 * @package Dgame\Functional\Option
 */
final class Some extends Option
{
    use SafeUnwrapTrait;

    /**
     * Some constructor.
     *
     * @param $value
     */
    public function __construct($value)
    {
        $this->values = [$value];
    }

    /**
     * @return mixed
     */
    public function unwrap()
    {
        return reset($this->values);
    }

    /**
     * @return bool
     */
    public function isSome(): bool
    {
        return true;
    }
}
