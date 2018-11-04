<?php

namespace Dgame\Functional\ADT;

/**
 * @param string   $name
 * @param callable $typecheck
 * @param mixed    $value
 *
 * @return TypeClosure
 */
function typeof(string $name, callable $typecheck, &$value): TypeClosure
{
    return new TypeClosure($name, $typecheck, $value);
}

/**
 * @param int $value
 *
 * @return TypeClosure
 */
function int(?int &$value): TypeClosure
{
    return typeof(__FUNCTION__, 'is_int', $value);
}

/**
 * @return bool
 */
function axiom()
{
    return true;
}

/**
 * @param mixed $value
 *
 * @return TypeClosure
 */
function any(&$value): TypeClosure
{
    return typeof(__FUNCTION__, 'axiom', $value);
}

/**
 * @param string $value
 *
 * @return TypeClosure
 */
function string(?string &$value): TypeClosure
{
    return typeof(__FUNCTION__, 'is_string', $value);
}

/**
 * @param float $value
 *
 * @return TypeClosure
 */
function float(?float &$value): TypeClosure
{
    return typeof(__FUNCTION__, 'is_float', $value);
}

/**
 * @param float|int $value
 *
 * @return TypeClosure
 */
function numeric(&$value): TypeClosure
{
    return typeof(__FUNCTION__, 'is_numeric', $value);
}

/**
 * @param mixed $value
 *
 * @return TypeClosure
 */
function scalar(&$value): TypeClosure
{
    return typeof(__FUNCTION__, 'is_scalar', $value);
}

/**
 * @param bool $value
 *
 * @return TypeClosure
 */
function bool(?bool &$value): TypeClosure
{
    return typeof(__FUNCTION__, 'is_bool', $value);
}

/**
 * @param DestructInterface $destructure
 *
 * @return LetBinding
 */
function let(DestructInterface $destructure): LetBinding
{
    return new LetBinding($destructure);
}
