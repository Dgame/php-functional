<?php

namespace Dgame\Functional\ADT;

/**
 * Class Tuple
 * @package Dgame\Functional\ADT
 */
final class Tuple implements ADTInterface
{
    use ADTTrait;
    use SafeUnwrapTrait;

    /**
     * Tuple constructor.
     *
     * @param mixed ...$values
     */
    public function __construct(...$values)
    {
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function unwrap()
    {
        return $this->destruct();
    }
}
