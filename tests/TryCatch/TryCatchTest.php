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

        $this->assertTrue($try->isSuccess($value));
        $this->assertEquals(42, $value);
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
        $this->assertTrue($try->isSuccess($value));
        $this->assertEquals(23, $value);
    }

    public function testGet(): void
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(42, $try->get());

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('What is this madness?');
        $try->get();
    }

    public function testGetOr(): void
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(42, $try->getOr(23));

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $this->assertEquals(23, $try->getOr(23));
        $try->ignoreFailure();
    }

    public function testGetOrElse(): void
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(42, $try->getOrElse(function () {
            return 23;
        }));

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $this->assertEquals(23, $try->getOrElse(function () {
            return 23;
        }));
        $try->ignoreFailure();
    }

    public function testGetOrThrow(): void
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(42, $try->getOrThrow('Not 42?!'));

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('What is this madness?');
        $try->getOrThrow('No!');
    }

    public function testFilter(): void
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $try = $try->filter(function (int $n) {
            return $n === 42;
        });
        $this->assertTrue($try->isSuccess());
        $this->assertEquals(42, $try->get());

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

    public function testMap(): void
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $try = $try->map(function (int $n) {
            return $n / 2 + 2;
        });
        $this->assertTrue($try->isSuccess());
        $this->assertEquals(23, $try->get());

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $try = $try->map(function (int $n) {
            return $n / 2 + 2;
        });
        $this->assertTrue($try->isFailed());
        $try->ignoreFailure();
    }

    public function testToOption(): void
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

    public function testToResult(): void
    {
        $try = new TryCatch(function () {
            return 42;
        });
        $this->assertEquals(new Ok(42), $try->toResult());

        $try = new TryCatch(function (): void {
            throw new Exception('What is this madness?');
        });
        $result = $try->toResult();
        $this->assertTrue($result->isErr());
        $exception = $result->unwrapErr();
        $this->assertEquals('What is this madness?', $exception->getMessage());
    }
}
