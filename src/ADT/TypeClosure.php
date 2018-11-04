<?php

namespace Dgame\Functional\ADT;

/**
 * Class TypeClosure
 * @package Dgame\Functional\ADT
 */
final class TypeClosure
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var callable
     */
    private $typecheck;
    /**
     * @var mixed
     */
    private $value;

    /**
     * TypeClosure constructor.
     *
     * @param string   $name
     * @param callable $typecheck
     * @param mixed    $value
     */
    public function __construct(string $name, callable $typecheck, &$value)
    {
        $this->name      = $name;
        $this->typecheck = $typecheck;
        $this->value     = &$value;
    }

    /**
     * @param $param
     *
     * @return bool
     */
    public function __invoke($param): bool
    {
        $this->value = ($this->typecheck)($param) ? $param : _;

        return $this->value !== _;
    }
}
