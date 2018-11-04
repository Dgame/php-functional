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
     * @param mixed ...$predicates
     *
     * @return bool
     */
    public function be(...$predicates): bool
    {
        $params = $this->adt->destruct();
        if (count($params) !== count($predicates)) {
            return false;
        }

        foreach ($predicates as $index => $predicate) {
            $param = $params[$index];

            if (is_callable($predicate)) {
                if (!$predicate($param)) {
                    return false;
                }

                continue;
            }

            if ($predicate !== $param) {
                return false;
            }
        }

        return true;
    }
}
