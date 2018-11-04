<?php

namespace Dgame\Functional\Test\TryCatch;

use Dgame\Functional\TryCatch\TryCatch;
use Exception;
use PHPUnit\Framework\TestCase;

final class TryCatchTest extends TestCase
{
    public function testIsSuccess()
    {
        $try = new TryCatch(function () {
            return 42;
        });

        $this->assertTrue($try->isSuccess($value));
        $this->assertEquals(42, $value);
    }

    public function testIsFailed()
    {
        $try = new TryCatch(function () {
            throw new Exception('What is this madness?');
        });

        $this->assertTrue($try->isFailed());
        $try = $try->recoverWith(function (Exception $e) {
            return $e->getMessage() === 'What is this madness?' ? 23 : $e;
        });
        $this->assertTrue($try->isSuccess($value));
        $this->assertEquals(23, $value);
    }
}
