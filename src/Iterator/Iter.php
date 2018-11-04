<?php

namespace Dgame\Functional\Iterator;

use Closure;
use Dgame\Functional\Option\Option;
use Generator;

/**
 * Class Iter
 * @package Dgame\Functional\Iterator
 */
final class Iter
{
    /**
     * @var Closure
     */
    private $closure;
    /**
     * @var Generator
     */
    private $generator;

    /**
     * Iter constructor.
     *
     * @param Closure $closure
     */
    private function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * @param Closure $closure
     *
     * @return Iter
     */
    public static function with(Closure $closure): self
    {
        return new self($closure);
    }

    /**
     * @param array $values
     *
     * @return Iter
     */
    public static function of(array $values): self
    {
        return self::with(function () use ($values) {
            foreach ($values as $value) {
                yield $value;
            }
        });
    }

    /**
     * @param string $text
     *
     * @return Iter
     */
    public static function chars(string $text): self
    {
        return self::with(function () use ($text) {
            for ($i = 0, $len = strlen($text); $i < $len; $i++) {
                yield $text[$i];
            }
        });
    }

    /**
     * @param $start
     * @param $end
     *
     * @return Iter
     */
    public static function range($start, $end): self
    {
        return self::with(function () use ($start, $end) {
            for ($i = $start; $i < $end; $i++) {
                yield $i;
            }

            yield $i; // Inclusive
        });
    }

    /**
     * @param int $start
     * @param int $end
     * @param int $step
     *
     * @return Iter
     */
    public static function iota(int $start, int $end, int $step = 1): self
    {
        return self::with(function () use ($start, $end, $step) {
            for ($i = $start; $i < $end; $i += $step) {
                yield $i;
            }

            yield $i; // Inclusive
        });
    }

    /**
     * @param Iter $it
     * @param int  $count
     *
     * @return Iter
     */
    public static function stride(self $it, int $count): self
    {
        return self::with(function () use ($it, $count) {
            $gen = $it->consume();
            for ($i = 0; $gen->valid(); $gen->next(), $i++) {
                if ($i % $count === 0) {
                    yield $gen->current();
                }
            }
        });
    }

    /**
     * @param Iter $it
     *
     * @return Iter
     */
    public static function cycle(self $it): self
    {
        return self::with(function () use ($it) {
            while (true) {
                $gen = $it->gen();
                for (; $gen->valid(); $gen->next()) {
                    yield $gen->current();
                }
            }
        });
    }

    /**
     * @param          $value
     * @param int|null $limit
     *
     * @return Iter
     */
    public static function repeat($value, int $limit = null): self
    {
        return self::with(function () use ($value, $limit) {
            $i = 0;
            while (true) {
                yield $value;

                if ($limit === null) {
                    continue;
                }

                $i++;
                if ($i >= $limit) {
                    break;
                }
            }
        });
    }

    /**
     * @param array $values
     * @param int   $n
     *
     * @return Iter
     */
    public static function chunks(array $values, int $n): self
    {
        return self::with(function () use ($values, $n) {
            $len   = count($values);
            $index = 0;
            for ($i = 0, $chunks = $len / $n; $i < $chunks; $i++) {
                $chunk = [];
                for ($j = 0; $index < $len && $j < $n; $j++) {
                    $chunk[] = $values[$index++];
                }

                yield $chunk;
            }
        });
    }

    /**
     * @param Closure $closure
     *
     * @return Iter
     */
    public function filter(Closure $closure): self
    {
        return new self(function () use ($closure) {
            $gen = $this->consume();
            for (; $gen->valid(); $gen->next()) {
                $value = $gen->current();
                if ($closure($value)) {
                    yield $value;
                }
            }
        });
    }

    /**
     * @param Closure      $closure
     * @param Closure|null $validate
     *
     * @return Iter
     */
    public function filterMap(Closure $closure, Closure $validate = null): self
    {
        if ($validate === null) {
            $validate = function ($value) {
                return $value !== null;
            };
        }

        return new self(function () use ($closure, $validate) {
            $gen = $this->consume();
            for (; $gen->valid(); $gen->next()) {
                $value  = $gen->current();
                $result = $closure($value);
                if ($validate($result)) {
                    yield $result;
                }
            }
        });
    }

    /**
     * @param Closure $closure
     *
     * @return Iter
     */
    public function map(Closure $closure): self
    {
        return new self(function () use ($closure) {
            $gen = $this->consume();
            for (; $gen->valid(); $gen->next()) {
                $value = $gen->current();
                yield $closure($value);
            }
        });
    }

    /**
     * @param int $n
     *
     * @return Iter
     */
    public function take(int $n): self
    {
        return new self(function () use ($n) {
            $gen = $this->consume();
            for ($i = 0; $gen->valid(); $gen->next(), $i++) {
                if ($i >= $n) {
                    break;
                }

                $value = $gen->current();
                yield $value;
            }
        });
    }

    /**
     * @param Closure $closure
     *
     * @return Iter
     */
    public function takeWhile(Closure $closure): self
    {
        return new self(function () use ($closure) {
            $gen = $this->consume();
            for (; $gen->valid(); $gen->next()) {
                $value = $gen->current();
                if (!$closure($value)) {
                    break;
                }

                yield $value;
            }
        });
    }

    /**
     * @param Closure $closure
     *
     * @return Iter
     */
    public function takeUntil(Closure $closure): self
    {
        return new self(function () use ($closure) {
            $gen = $this->consume();
            for (; $gen->valid(); $gen->next()) {
                $value = $gen->current();
                if ($closure($value)) {
                    break;
                }

                yield $value;
            }
        });
    }

    /**
     * @param int $n
     *
     * @return Iter
     */
    public function skip(int $n): self
    {
        return new self(function () use ($n) {
            $gen = $this->consume();
            for ($i = 0; $gen->valid(); $gen->next(), $i++) {
                if ($i >= $n) {
                    yield $gen->current();
                }
            }
        });
    }

    /**
     * @param Closure $closure
     *
     * @return Iter
     */
    public function skipWhile(Closure $closure): self
    {
        return new self(function () use ($closure) {
            $skip = true;
            $gen  = $this->consume();
            for (; $gen->valid(); $gen->next()) {
                $value = $gen->current();
                if ($skip && $closure($value)) {
                    continue;
                }

                $skip = false;

                yield $value;
            }
        });
    }

    /**
     * @param Closure $closure
     *
     * @return Iter
     */
    public function skipUntil(Closure $closure): self
    {
        return new self(function () use ($closure) {
            $skip = true;
            $gen  = $this->consume();
            for (; $gen->valid(); $gen->next()) {
                $value = $gen->current();
                if ($skip && !$closure($value)) {
                    continue;
                }

                $skip = false;

                yield $value;
            }
        });
    }

    /**
     * @param Iter ...$tails
     *
     * @return Iter
     */
    public function chain(self ...$tails): self
    {
        return self::with(function () use ($tails) {
            $gen = $this->consume();
            for (; $gen->valid(); $gen->next()) {
                yield $gen->current();
            }

            /** @var Generator $gent1 */
            $gent1 = $tails();
            for (; $gent1->valid(); $gent1->next()) {
                $tail = $gent1->current();
                /** @var Generator $gent2 */
                $gent2 = $tail();
                for (; $gent2->valid(); $gent2->next()) {
                    $value = $gent2->current();
                    yield $value;
                }
            }
        });
    }

    /**
     * @param Iter ...$tails
     *
     * @return Iter
     */
    public function zip(self ...$tails): self
    {
        return self::with(function () use ($tails) {
            /** @var Generator $gent1 */
            $gent1 = $tails();
            for (; $gent1->valid(); $gent1->next()) {
                $tail = $gent1->current();
                /** @var Generator $gent2 */
                $gent2 = $tail();
                for (; $gent2->valid(); $gent2->next()) {
                    yield $gent1->current() => $gent2->current();
                }
            }
        });
    }

    /**
     * @return Iter
     */
    public function enumerate(): self
    {
        return new self(function () {
            $gen = $this->consume();
            for ($i = 0; $gen->valid(); $gen->next(), $i++) {
                yield $i => $gen->current();
            }
        });
    }

    /**
     * @param Closure $closure
     * @param null    $acc
     *
     * @return mixed|null
     */
    public function fold(Closure $closure, $acc = null)
    {
        $gen = $this->consume();
        for (; $gen->valid(); $gen->next()) {
            $value = $gen->current();
            $acc   = $closure($acc, $value);
        }

        return $acc;
    }

    /**
     * @return float
     */
    public function sum(): float
    {
        return $this->fold(function (float $acc, float $value): float {
            return $acc + $value;
        }, 0);
    }

    /**
     * @param Closure $closure
     *
     * @return bool
     */
    public function any(Closure $closure): bool
    {
        $gen = $this->consume();
        for (; $gen->valid(); $gen->next()) {
            $value = $gen->current();
            if ($closure($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Closure $closure
     *
     * @return bool
     */
    public function all(Closure $closure): bool
    {
        $gen = $this->consume();
        for (; $gen->valid(); $gen->next()) {
            $value = $gen->current();
            if (!$closure($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function collect(): array
    {
        $output = [];
        $gen    = $this->consume();
        for (; $gen->valid(); $gen->next()) {
            $output[] = $gen->current();
        }

        return $output;
    }

    /**
     * @param string $glue
     *
     * @return string
     */
    public function implode(string $glue = ''): string
    {
        return implode($glue, $this->collect());
    }

    /**
     * @return Option
     */
    public function next(): Option
    {
        $gen   = $this->consume();
        $value = $gen->current();
        $gen->next();

        return Option::maybe($value);
    }

    /**
     * @return Iter
     */
    public function clone(): self
    {
        $it            = new self($this->closure);
        $it->generator = null;

        return $it;
    }

    /**
     * @return Generator
     */
    private function gen(): Generator
    {
        return ($this->closure)();
    }

    /**
     * @return Generator
     */
    public function consume(): Generator
    {
        if ($this->generator === null) {
            $this->generator = $this->gen();
        }

        return $this->generator;
    }
}
