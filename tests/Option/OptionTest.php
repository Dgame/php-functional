<?php

namespace Dgame\Functional\Test\Option;

use AssertionError;
use Dgame\Functional\Option\None;
use Dgame\Functional\Option\Option;
use Dgame\Functional\Option\Some;
use Dgame\Functional\Result\Err;
use Dgame\Functional\Result\Ok;
use PHPUnit\Framework\TestCase;
use function Dgame\Functional\ADT\float;
use function Dgame\Functional\ADT\int;
use function Dgame\Functional\ADT\let;

final class OptionTest extends TestCase
{
    public function testIsSomeIsNone()
    {
        $opt = new Some(42);
        $this->assertTrue($opt->isSome());
        $this->assertFalse($opt->isNone());

        $opt = new None();
        $this->assertFalse($opt->isSome());
        $this->assertTrue($opt->isNone());
    }

    public function testLetBinding()
    {
        $opt = new Some(42);
        $this->assertTrue(let($opt)->be(int($a)));
        $this->assertTrue(is_int($a));
        $this->assertEquals(42, $a);

        $this->assertTrue(let($opt)->be(42));

        $opt = new None();
        $this->assertFalse(let($opt)->be(int($b)));
        $this->assertNull($b);
    }

    public function testExpect()
    {
        $x = new Some('value');
        $this->assertEquals('value', $x->expect('the world is ending'));

        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('the world is ending');

        $y = new None();
        $y->expect('the world is ending');
    }

    public function testUnwrap()
    {
        $x = new Some('air');
        $this->assertEquals('air', $x->unwrap());

        $y = new None();
        $this->expectException(AssertionError::class);
        $this->expectExceptionMessage('Unwraped None');
        $y->unwrap();
    }

    public function testUnwrapOr()
    {
        $x = new Some('car');
        $this->assertEquals('car', $x->unwrapOr('bike'));

        $y = new None();
        $this->assertEquals('bike', $y->unwrapOr('bike'));
    }

    public function testUnwrapOrElse()
    {
        $k = 10;
        $x = new Some(4);
        $this->assertEquals(4, $x->unwrapOrElse(function () use ($k) {
            return 2 * $k;
        }));

        $y = new None();
        $this->assertEquals(20, $y->unwrapOrElse(function () use ($k) {
            return 2 * $k;
        }));
    }

    public function testMap()
    {
        $x = new Some('Hello World');
        $y = $x->map(function (string $s) {
            return strlen($s);
        });
        $this->assertTrue($y->isSome());
        $this->assertEquals(11, $y->unwrap());
    }

    public function testMapOr()
    {
        $x = new Some('foo');
        $this->assertEquals(3, $x->mapOr(function (string $s) {
            return strlen($s);
        }, 42));

        $y = new None();
        $this->assertEquals(42, $y->mapOr(function (string $s) {
            return strlen($s);
        }, 42));
    }

    public function testMaprOrElse()
    {
        $k = 21;
        $x = new Some('foo');
        $this->assertEquals(3, $x->mapOrElse(function (string $s) {
            return strlen($s);
        }, function () use ($k) {
            return 2 * $k;
        }));

        $y = new None();
        $this->assertEquals(42, $y->mapOrElse(function (string $s) {
            return strlen($s);
        }, function () use ($k) {
            return 2 * $k;
        }));
    }

    public function testOkOr()
    {
        $x = new Some('foo');
        $this->assertEquals(new Ok('foo'), $x->okOr(0));

        $y = new None();
        $this->assertEquals(new Err(0), $y->okOr(0));
    }

    public function testOkOrElse()
    {
        $x = new Some('foo');
        $this->assertEquals(new Ok('foo'), $x->okOrElse(function () {
            return 0;
        }));

        $y = new None();
        $this->assertEquals(new Err(0), $y->okOrElse(function () {
            return 0;
        }));
    }

    public function testAnd()
    {
        $x = new Some(2);
        $y = new None();

        $this->assertTrue($x->and($y)->isNone());

        $y = new Some('foo');
        $x = new None();

        $this->assertTrue($x->and($y)->isNone());

        $y = new Some('foo');
        $x = new Some(2);

        $this->assertEquals(new Some('foo'), $x->and($y));

        $x = new None();
        $y = new None();

        $this->assertTrue($x->and($y)->isNone());
        $this->assertTrue($y->and($x)->isNone());
    }

    public function testAndThen()
    {
        $sq = function (int $x): Option {
            return new Some($x * $x);
        };

        $nope = function (): Option {
            return new None();
        };

        $x = new Some(2);
        $this->assertEquals(new Some(16), $x->andThen($sq)->andThen($sq));
        $this->assertEquals(new None(), $x->andThen($sq)->andThen($nope));
        $this->assertEquals(new None(), $x->andThen($nope)->andThen($sq));
        $this->assertEquals(new None(), $x->andThen($nope)->andThen($nope));

        $y = new None();
        $this->assertEquals(new None(), $y->andThen($sq)->andThen($sq));
    }

    public function testOr()
    {
        $x = new Some(2);
        $y = new None();

        $this->assertEquals(new Some(2), $x->or($y));

        $x = new None();
        $y = new Some(100);

        $this->assertEquals(new Some(100), $x->or($y));

        $x = new Some(2);
        $y = new Some(100);

        $this->assertEquals(new Some(2), $x->or($y));

        $x = new None();
        $y = new None();

        $this->assertEquals(new None(), $x->or($y));
    }

    public function testOrElse()
    {
        $nobody  = function (): Option {
            return new None();
        };
        $vikings = function (): Option {
            return new Some('vikings');
        };

        $x = new Some('barbarians');
        $this->assertEquals(new Some('barbarians'), $x->orElse($vikings));
        $y = new None();
        $this->assertEquals(new Some('vikings'), $y->orElse($vikings));
        $this->assertEquals($y, $y->orElse($nobody));
    }

    public function testFilter()
    {
        $isEven = function (int $n): bool {
            return $n % 2 === 0;
        };

        $a = new None();
        $this->assertTrue($a->filter($isEven)->isNone());
        $b = new Some(3);
        $this->assertTrue($b->filter($isEven)->isNone());
        $c = new Some(4);
        $this->assertTrue($c->filter($isEven)->isSome());
    }

    public function testSwitch()
    {
        $switches = 0;

        $opt = new Some(23);
        $opt->matchFirst([Some::matches('*') => function (int $value) use (&$switches) {
            $switches++;
            $this->assertEquals(23, $value);
        }]);
        $opt->matchFirst([Some::matches() => function () use (&$switches) {
            $switches++;
        }]);
        $opt->matchFirst([Some::matches(_) => function () use (&$switches) {
            $switches++;
        }]);
        $opt->matchFirst(
            [
                None::matches()              => function () {
                    $this->fail('Not None!');
                },
                Some::matches(float($value)) => function (float $_) {
                    $this->fail('Not a float!');
                },
                Some::matches(int($value))   => function (int $value) use (&$switches) {
                    $switches++;
                    $this->assertEquals(23, $value);
                }
            ]
        );

        $this->assertEquals(4, $switches);

        $opt = new None();
        $opt->matchFirst([Some::matches() => function ($_) {
            $this->fail('Not Some');
        }]);
        $opt->matchFirst([Some::matches(_) => function () {
            $this->fail('Not Some');
        }]);
        $opt->matchFirst(
            [
                Some::matches(_) => function () {
                    $this->fail('Not Some');
                },
                None::matches()  => function () use (&$switches) {
                    $switches++;
                }
            ]
        );
        $opt->matchFirst(
            [
                None::matches(_) => function () use (&$switches) {
                    $switches++;
                }
            ]
        );

        $this->assertEquals(6, $switches);
    }
}
