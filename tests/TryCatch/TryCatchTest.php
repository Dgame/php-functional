<?php

namespace Dgame\Functional\Test\TryCatch;

use Dgame\Functional\Option\None;
use Dgame\Functional\Option\Some;
use Dgame\Functional\Result\Ok;
use Dgame\Functional\TryCatch\TryCatch;
use Exception;
use PHPUnit\Framework\TestCase;

final class TryCatchTest extends TestCase
{
    public function testIsSuccess(): void
    {
        $try = new TryCatch(function () {
            return 42;
        });

        $this->assertTrue($try->isSuccess());
        $this->assertEquals(42, $try->unwrap());
    }

    public function testIsFailed(): void
    {
        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });

        $this->assertTrue($try->isFailed());
        $try = $try->recoverWith(function (Exception $e) {
            return $e->getMessage() === 'What is this madness?' ? 23 : $e;
        });
        $this->assertTrue($try->isSuccess());
        $this->assertEquals(23, $try->unwrap());
    }

    public function testGet()
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(42, $try->unwrap());

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('What is this madness?');
        $try->unwrap();
    }

    public function testGetOr()
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(42, $try->unwrapOr(23));

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $this->assertEquals(23, $try->unwrapOr(23));
        $try->ignoreFailure();
    }

    public function testGetOrElse()
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(42, $try->unwrapOrElse(function () {
            return 23;
        }));

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $this->assertEquals(23, $try->unwrapOrElse(function () {
            return 23;
        }));
        $try->ignoreFailure();
    }

    public function testGetOrThrow()
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(42, $try->unwrapOrThrow('Not 42?!'));

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('What is this madness?');
        $try->unwrapOrThrow('No!');
    }

    public function testFilter()
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $try = $try->filter(function (int $n) {
            return $n === 42;
        });
        $this->assertTrue($try->isSuccess());
        $this->assertEquals(42, $try->unwrap());

        $try = $try->filter(function (int $n) {
            return $n > 42;
        });
        $this->assertTrue($try->isFailed());
        $try->ignoreFailure();

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $try = $try->filter(function (int $n) {
            return $n > 42;
        });
        $this->assertTrue($try->isFailed());
        $try->ignoreFailure();
    }

    public function testMap()
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $try = $try->map(function (int $n) {
            return $n / 2 + 2;
        });
        $this->assertTrue($try->isSuccess());
        $this->assertEquals(23, $try->unwrap());

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $try = $try->map(function (int $n) {
            return $n / 2 + 2;
        });
        $this->assertTrue($try->isFailed());
        $try->ignoreFailure();
    }

    public function testToOption()
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(new Some(42), $try->toOption());

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $this->assertEquals(new None(), $try->toOption());
    }

    public function testToResult()
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(new Ok(42), $try->toResult());

        $try    = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $result = $try->toResult();
        $this->assertTrue($result->isErr());
        $exception = $result->unwrapErr();
        $this->assertEquals('What is this madness?', $exception->getMessage());
    }
}
