<?php

namespace Dgame\Functional\ADT;

defined('_') or define('_', null);

/**
 * Trait MatchTrait
 * @package Dgame\Functional\ADT
 */
trait MatchTrait
{
    use DestructTrait;

    /**
     * @param mixed ...$args
     *
     * @return string
     */
    public static function matches(...$args)
    {
        $mangle = [];
        foreach ($args as $arg) {
            if ($arg !== _) {
                if (is_callable($arg)) {
                    $mangle[] = serialize($arg);
                } else {
                    $mangle[] = (string) $arg;
                }
            } else {
                $mangle[] = '_';
            }
        }

        return sprintf('%s(%s)', static::class, implode(',', $mangle));
    }

    /**
     * @param string     $mangle
     * @param array|null $params
     *
     * @return bool
     */
    private function isMatch(string $mangle, array &$params = null): bool
    {
        $params = [];

        $pattern = sprintf('/%s\((.*?)\)/iS', preg_quote(static::class));
        if (preg_match($pattern, $mangle, $matches) !== 1) {
            return false;
        }

        if (empty($matches[1]) || $matches[1] === '_') {
            return true;
        }

        if ($matches[1] === '*') {
            $params = $this->values;

            return true;
        }

        foreach (explode(',', $matches[1]) as $index => $arg) {
            if (!array_key_exists($index, $this->values)) {
                return false;
            }

            if ($arg === '_') {
                continue;
            }

            $value = $this->values[$index];

            $callback = @unserialize($arg);
            if (is_callable($callback)) {
                if (!$callback($value)) {
                    return false;
                }

                $params[] = $value;

                continue;
            }

            if ($arg != $value) {
                return false;
            }

            $params[] = $value;
        }

        return true;
    }

    /**
     * @param array $cases
     */
    public function matchFirst(array $cases): void
    {
        foreach ($cases as $mangle => $closure) {
            if ($this->isMatch($mangle, $params)) {
                $closure(...$params);
                break;
            }
        }
    }
}
