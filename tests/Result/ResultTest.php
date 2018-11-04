<?php

namespace Dgame\Functional\Test\Result;

use AssertionError;
use Dgame\Functional\Result\Err;
use Dgame\Functional\Result\Ok;
use Dgame\Functional\Result\Result;
use PHPUnit\Framework\TestCase;
use function Dgame\Functional\ADT\float;
use function Dgame\Functional\ADT\int;
use function Dgame\Functional\ADT\let;

/**
 * Class ResultTest
 * @package Dgame\Functional\Test\Result
 */
final class ResultTest extends TestCase
{
    public function testIsOkIsErr(): void
    {
        $result = new Ok(42);
        $this->assertTrue($result->isOk());
        $this->assertFalse($result->isErr());

        $result = new Err('No!');
        $this->assertFalse($result->isOk());
        $this->assertTrue($result->isErr());
    }

    public function testLetBinding(): void
    {
        $result = new Ok(42);
        $this->assertTrue(let($result)->be(int($a)));
        $this->assertTrue(is_int($a));
        $this->assertEquals(42, $a);

        $this->assertTrue(let($result)->be(42));

        $result = new Err('No!');
        $this->assertFalse(let($result)->be(int($b)));
        $this->assertNull($b);
    }

    public function testExpect(): void
    {
        $x = new Ok('value');
        $this->assertEquals('value', $x->expect('the world is ending'));

        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('the world is ending');

        $y = new Err('No!');
        $y->expect('the world is ending: No!');
    }

    public function testUnwrap(): void
    {
        $x = new Ok('air');
        $this->assertEquals('air', $x->unwrap());

        $y = new Err('No!');
        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('Unwraped Err: No!');
        $y->unwrap();
    }

    public function testUnwrapOr(): void
    {
        $x = new Ok('car');
        $this->assertEquals('car', $x->unwrapOr('bike'));

        $y = new Err('No!');
        $this->assertEquals('bike', $y->unwrapOr('bike'));
    }

    public function testUnwrapOrElse(): void
    {
        $k = 10;
        $x = new Ok(4);
        $this->assertEquals(4, $x->unwrapOrElse(function () use ($k) {
            return 2 * $k;
        }));

        $y = new Err('No!');
        $this->assertEquals(20, $y->unwrapOrElse(function () use ($k) {
            return 2 * $k;
        }));
    }

    public function testMap(): void
    {
        $x = new Ok('Hello World');
        $y = $x->map(function (string $s) {
            return strlen($s);
        });
        $this->assertTrue($y->isOk());
        $this->assertEquals(11, $y->unwrap());
    }

    public function testMapOr(): void
    {
        $x = new Ok('foo');
        $this->assertEquals(3, $x->mapOr(function (string $s) {
            return strlen($s);
        }, 42));

        $y = new Err('No!');
        $this->assertEquals(42, $y->mapOr(function (string $s) {
            return strlen($s);
        }, 42));
    }

    public function testMaprOrElse(): void
    {
        $k = 21;
        $x = new Ok('foo');
        $this->assertEquals(3, $x->mapOrElse(function (string $s) {
            return strlen($s);
        }, function () use ($k) {
            return 2 * $k;
        }));

        $y = new Err('No!');
        $this->assertEquals(42, $y->mapOrElse(function (string $s) {
            return strlen($s);
        }, function () use ($k) {
            return 2 * $k;
        }));
    }

    public function testAnd(): void
    {
        $x = new Ok(2);
        $y = new Err('No!');

        $this->assertTrue($x->and($y)->isErr());

        $y = new Ok('foo');
        $x = new Err('No!');

        $this->assertTrue($x->and($y)->isErr());

        $y = new Ok('foo');
        $x = new Ok(2);

        $this->assertEquals(new Ok('foo'), $x->and($y));

        $x = new Err('No!');
        $y = new Err('No!');

        $this->assertTrue($x->and($y)->isErr());
        $this->assertTrue($y->and($x)->isErr());
    }

    public function testAndThen(): void
    {
        $sq = function (int $x): Result {
            return new Ok($x * $x);
        };

        $nope = function (): Result {
            return new Err('No!');
        };

        $x = new Ok(2);
        $this->assertEquals(new Ok(16), $x->andThen($sq)->andThen($sq));
        $this->assertEquals(new Err('No!'), $x->andThen($sq)->andThen($nope));
        $this->assertEquals(new Err('No!'), $x->andThen($nope)->andThen($sq));
        $this->assertEquals(new Err('No!'), $x->andThen($nope)->andThen($nope));

        $y = new Err('No!');
        $this->assertEquals(new Err('No!'), $y->andThen($sq)->andThen($sq));
    }

    public function testOr(): void
    {
        $x = new Ok(2);
        $y = new Err('No!');

        $this->assertEquals(new Ok(2), $x->or($y));

        $x = new Err('No!');
        $y = new Ok(100);

        $this->assertEquals(new Ok(100), $x->or($y));

        $x = new Ok(2);
        $y = new Ok(100);

        $this->assertEquals(new Ok(2), $x->or($y));

        $x = new Err('No!');
        $y = new Err('No!');

        $this->assertEquals(new Err('No!'), $x->or($y));
    }

    public function testOrElse(): void
    {
        $nobody  = function (): Result {
            return new Err('No!');
        };
        $vikings = function (): Result {
            return new Ok('vikings');
        };

        $x = new Ok('barbarians');
        $this->assertEquals(new Ok('barbarians'), $x->orElse($vikings));
        $y = new Err('No!');
        $this->assertEquals(new Ok('vikings'), $y->orElse($vikings));
        $this->assertEquals($y, $y->orElse($nobody));
    }

    public function testUnwrapErr(): void
    {
        $a = new Ok(42);
        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('42');
        $a->unwrapErr();

        $b = new Err('Fail');
        $this->assertEquals('Fail', $b->unwrapErr());
    }

    public function testExpectErr(): void
    {
        $a = new Ok(42);
        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('Unexpected: 42');
        $a->expectErr('Unexpected');

        $b = new Err('Fail');
        $this->assertEquals('Fail', $b->expectErr('Expected'));
    }

    public function testSwitch(): void
    {
        $switches = 0;

        $result = new Ok(23);
        $result->matchFirst([Ok::matches('*') => function (int $value) use (&$switches): void {
            $switches++;
            $this->assertEquals(23, $value);
        }]);
        $result->matchFirst([Ok::matches() => function () use (&$switches): void {
            $switches++;
        }]);
        $result->matchFirst([Ok::matches(_) => function () use (&$switches): void {
            $switches++;
        }]);
        $result->matchFirst(
            [
                Err::matches()              => function (): void {
                    $this->fail('Not Err!');
                },
                Err::matches(float($value)) => function (float $_): void {
                    $this->fail('Not a float!');
                },
                Ok::matches(int($value))    => function (int $value) use (&$switches): void {
                    $switches++;
                    $this->assertEquals(23, $value);
                }
            ]
        );

        $this->assertEquals(4, $switches);

        $result = new Err('No!');
        $result->matchFirst([Ok::matches() => function ($_): void {
            $this->fail('Not Ok');
        }]);
        $result->matchFirst([Ok::matches(_) => function (): void {
            $this->fail('Not Ok');
        }]);
        $result->matchFirst(
            [
                Ok::matches(_) => function (): void {
                    $this->fail('Not Ok');
                },
                Err::matches() => function () use (&$switches): void {
                    $switches++;
                }
            ]
        );
        $result->matchFirst(
            [
                Err::matches(_) => function () use (&$switches): void {
                    $switches++;
                }
            ]
        );

        $this->assertEquals(6, $switches);
    }
}
