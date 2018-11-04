<?php

namespace Dgame\Functional\Option;

use Dgame\Functional\ADT\DefaultUnwrapTrait;

/**
 * Class None
 * @package Dgame\Functional\Option
 */
final class None extends Option
{
    use DefaultUnwrapTrait;

    /**
     * @return void
     */
    public function unwrap()
    {
        $this->expect('Unwraped None');
    }

    /**
     * @return bool
     */
    public function isSome(): bool
    {
        return false;
    }
}
