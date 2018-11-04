<?php

namespace Dgame\Functional\Test\Iterator;

use Dgame\Functional\Iterator\Iter;
use PHPUnit\Framework\TestCase;

final class IteratorTest extends TestCase
{
    public function testNext(): void
    {
        $it = Iter::chars('Hallo');
        $this->assertEquals('H', $it->next()->unwrap());
        $this->assertEquals('a', $it->next()->unwrap());
        $this->assertEquals('l', $it->next()->unwrap());
        $this->assertEquals('l', $it->next()->unwrap());
        $this->assertEquals('o', $it->next()->unwrap());
        $this->assertTrue($it->next()->isNone());
        $this->assertEmpty($it->collect());

        $it = Iter::of([1, 0, false, 23, null, 42]);
        $this->assertEquals(1, $it->next()->unwrap());
        $this->assertEquals(0, $it->next()->unwrap());
        $this->assertEquals(false, $it->next()->unwrap());
        $this->assertEquals(23, $it->next()->unwrap());
        $this->assertTrue($it->next()->isNone());
        $this->assertEquals(42, $it->next()->unwrap());
        $this->assertTrue($it->next()->isNone());
        $this->assertEmpty($it->collect());
    }

    public function testChars(): void
    {
        $it = Iter::chars('Hallo');
        $this->assertEquals('H', $it->next()->unwrap());
        $this->assertEquals('a', $it->next()->unwrap());
        $this->assertEquals('l', $it->next()->unwrap());

        $this->assertEquals('lo', $it->implode());
        $this->assertEmpty($it->implode());
    }

    public function testRange(): void
    {
        $it = Iter::range('a', 'z');
        $this->assertEquals('a', $it->next()->unwrap());
        $this->assertEquals('b', $it->next()->unwrap());
        $this->assertEquals('c', $it->next()->unwrap());

        $this->assertEquals('defghijkl', $it->take(9)->implode());
    }

    public function testIota(): void
    {
        $this->assertEquals([0, 1, 2, 3, 4, 5, 6], Iter::iota(0, 100)->take(7)->collect());
        $this->assertEquals([0, 2, 4, 6, 8, 10, 12], Iter::iota(0, 100, 2)->take(7)->collect());
    }

    public function testStride(): void
    {
        $this->assertEquals([1, 4, 7, 10], Iter::stride(Iter::iota(1, 11), 3)->collect());
    }

    public function testCylce(): void
    {
        $this->assertEquals([1, 2, 1, 2, 1], Iter::cycle(Iter::iota(1, 2))->take(5)->collect());
    }

    public function testRepeat(): void
    {
        $this->assertEquals([42, 42, 42, 42], Iter::repeat(42)->take(4)->collect());
        $this->assertEquals([42, 42, 42, 42, 42, 42, 42, 42], Iter::repeat(42, 18)->take(8)->collect());
        $this->assertEquals([42, 42, 42, 42, 42, 42, 42], Iter::repeat(42, 7)->take(8)->collect());
    }

    public function testChunks(): void
    {
        $this->assertEquals([[1, 2, 3, 4], [5, 6, 7, 8], [9]], Iter::chunks([1, 2, 3, 4, 5, 6, 7, 8, 9], 4)->collect());
    }

    public function testMap(): void
    {
        $it = Iter::of([1, 2, 3, 4])->map(function (int $i) {
            return $i * 2;
        });
        $this->assertEquals([2, 4, 6, 8], $it->collect());
    }

    public function testSum(): void
    {
        $it = Iter::of([1, 2, 3, 4]);
        $this->assertEquals(10, $it->clone()->sum());
        $it = $it->map(function (int $i) {
            return $i * 2;
        });
        $this->assertEquals(20, $it->sum());
        $this->assertEquals(0, $it->sum());
        $this->assertEquals(0, $it->clone()->sum());
    }

    public function testAny(): void
    {
        $it = Iter::of([1, 3, 5, 7, 9]);
        $this->assertFalse($it->any(function (int $i): bool {
            return $i % 2 === 0;
        }));
        $this->assertFalse($it->any(function (int $i): bool {
            return $i > 9;
        }));
        $it = Iter::of([2, 4, 6, 8]);
        $this->assertTrue($it->any(function (int $i): bool {
            return $i % 2 === 0;
        }));
        $this->assertTrue($it->any(function (int $i): bool {
            return $i > 1;
        }));
    }

    public function testAnyCollect(): void
    {
        $it = Iter::iota(0, 50)->take(25);
        $this->assertTrue($it->any(function (int $i): bool {
            return $i > 10;
        }));
        $this->assertEquals(range(11, 24), $it->collect());
    }

    public function testAll(): void
    {
        $it = Iter::of([1, 3, 5, 7, 9]);
        $this->assertFalse($it->all(function (int $i): bool {
            return $i % 2 === 0;
        }));
        $it = Iter::of([2, 4, 6, 8]);
        $this->assertTrue($it->all(function (int $i): bool {
            return $i % 2 === 0;
        }));
    }

    public function testAllCollect(): void
    {
        $it = Iter::iota(0, 50)->take(25);
        $this->assertFalse($it->all(function (int $i): bool {
            return $i > 10;
        }));
        $this->assertEquals(range(0, 24), $it->collect());

        $it = Iter::iota(0, 50)->take(25);
        $this->assertFalse($it->all(function (int $i): bool {
            return $i < 15;
        }));
        $this->assertEquals(range(15, 24), $it->collect());
    }

    public function testSkipTake(): void
    {
        $it = Iter::iota(0, 50)->skip(15)->take(15);
        $this->assertEquals(range(15, 15 + 15 - 1), $it->collect());

        $it = Iter::iota(0, 50)->take(15)->skip(5);
        $this->assertEquals(range(5, 14), $it->collect());
    }

    public function testFilter(): void
    {
        $it = Iter::iota(0, 10)->filter(function (int $i): bool {
            return $i % 2 === 0;
        });
        $this->assertEquals([0, 2, 4, 6, 8, 10], $it->collect());
    }

    public function testTakeWhile(): void
    {
        $belowTen = function (int $item) {
            return $item < 10;
        };
        $this->assertEquals([0, 1, 2], Iter::of([0, 1, 2, 10, 20])->takeWhile($belowTen)->collect());
    }

    public function testTakeUntil(): void
    {
        $belowTen = function (int $item) {
            return $item >= 10;
        };
        $this->assertEquals([0, 1, 2], Iter::of([0, 1, 2, 10, 20])->takeUntil($belowTen)->collect());
    }

    public function testSkipWhile(): void
    {
        $belowTen = function (int $item) {
            return $item < 10;
        };
        $this->assertEquals([10, 20], Iter::of([0, 1, 2, 10, 20])->skipWhile($belowTen)->collect());
    }

    public function testSkipUntil(): void
    {
        $belowTen = function (int $item) {
            return $item >= 10;
        };
        $this->assertEquals([10, 20], Iter::of([0, 1, 2, 10, 20])->skipUntil($belowTen)->collect());
    }
}
