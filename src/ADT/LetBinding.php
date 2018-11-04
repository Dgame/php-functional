<?php

namespace Dgame\Functional\ADT;

/**
 * Class LetBinding
 * @package Dgame\Functional\ADT
 */
final class LetBinding
{
    /**
     * @var DestructInterface
     */
    private $adt;

    /**
     * LetBinding constructor.
     *
     * @param DestructInterface $adt
     */
    public function __construct(DestructInterface $adt)
    {
        $this->adt = $adt;
    }

    /**
     * @param mixed ...$closures
     *
     * @return bool
     */
    public function be(...$closures): bool
    {
        $params = $this->adt->destruct();
        if (count($params) !== count($closures)) {
            return false;
        }

        foreach ($closures as $index => $closure) {
            if (array_key_exists($index, $params) && is_callable($closure)) {
                $param = $params[$index];
                if (!$closure($param)) {
                    return false;
                }
            }
        }

        return true;
    }
}
