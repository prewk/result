<?php

declare(strict_types=1);

namespace Prewk\Result\Test\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Prewk\Option\None;
use Prewk\Option\Some;
use Prewk\Result\Err;
use Prewk\Result\Ok;
use Prewk\Result\ResultException;

/**
 * @covers \Prewk\Result\Err
 * @uses \Prewk\Option\None
 * @uses \Prewk\Option\Some
 * @uses \Prewk\Result\Ok
 */
final class ErrTest extends TestCase
{
    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::isOk
     */
    public function testIsOkReturnsFalse(): void
    {
        $err = new Err('error');
        self::assertFalse($err->isOk());
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::isErr
     */
    public function testIsErrReturnsTrue(): void
    {
        $err = new Err('error');
        self::assertTrue($err->isErr());
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::ok
     */
    public function testOkReturnsNone(): void
    {
        $err = new Err('error');
        self::assertInstanceOf(None::class, $err->ok());
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::err
     */
    public function testErrReturnsSomeWithValue(): void
    {
        $errorMessage = 'error';
        $err = new Err($errorMessage);
        self::assertInstanceOf(Some::class, $err->err());
        self::assertEquals($errorMessage, $err->err()->unwrap());
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::map
     */
    public function testMapReturnsSelf(): void
    {
        $err = new Err('error');
        $mappedResult = $err->map(static function (): never {
            // This callback should not execute for an Err
            self::fail('Map callback should not execute for Err');
        });
        self::assertSame($err, $mappedResult);
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::mapOr
     */
    public function testMapOrReturnsDefault(): void
    {
        $err = new Err('error');
        $default = 'default';
        $result = $err->mapOr($default, static function (): never {
            // This callback should not execute for an Err
            self::fail('MapOr callback should not execute for Err');
        });
        self::assertEquals($default, $result);
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::mapOrElse
     */
    public function testMapOrElseReturnsDefault(): void
    {
        $err = new Err('error');
        $default = 'default';
        $result = $err->mapOrElse(
            static fn () => $default,
            static function (): never {
                // This callback should not execute for an Err
                self::fail('MapOrElse callback should not execute for Err');
            }
        );
        self::assertEquals($default, $result);
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::mapErr
     */
    public function testMapErrReturnsMappedError(): void
    {
        $errMessage = 'error';
        $err = new Err($errMessage);
        $newErrorMessage = 'new error';
        $mappedResult = $err->mapErr(static fn () => $newErrorMessage);
        self::assertInstanceOf(Err::class, $mappedResult);
        self::assertEquals($newErrorMessage, $mappedResult->err()->unwrap());
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::inspect
     */
    public function testInspectDoesNotExecuteCallback(): void
    {
        $err = new Err('error');
        $result = $err->inspect(static function (): never {
            // This callback should not execute for an Err
            self::fail('Inspect callback should not execute for Err');
        });
        self::assertSame($err, $result);
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::inspectErr
     */
    public function testInspectErrExecutesCallback(): void
    {
        $errMessage = 'error';
        $err = new Err($errMessage);
        $callbackExecuted = false;
        $result = $err->inspectErr(static function () use (&$callbackExecuted) {
            $callbackExecuted = true;
        });
        self::assertTrue($callbackExecuted);
        self::assertSame($err, $result);
    }

    /**
     * @covers \Prewk\Result\Err::iter
     */
    public function testIterOnErr(): void
    {
        $err = new Err('Error message');
        self::assertEmpty($err->iter());
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::unwrap
     */
    public function testUnwrapWithThrowable(): never
    {
        $throwable = new Exception('Test error');
        $err = new Err($throwable);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test error');

        $err->unwrap();
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::unwrap
     */
    public function testUnwrapWithNonThrowable(): never
    {
        $nonThrowable = 'Test error';
        $err = new Err($nonThrowable);

        $this->expectException(ResultException::class);
        $this->expectExceptionMessage('Unwrapped an Err');

        $err->unwrap();
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::expect
     */
    public function testExpectThrowsGivenException(): never
    {
        $expectedExceptionMessage = 'Expected exception message';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $err = new Err('error');
        $err->expect(new Exception($expectedExceptionMessage));
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::expectErr
     */
    public function testExpectErrReturnsErrorValue(): void
    {
        $errMessage = 'error';
        $err = new Err($errMessage);
        self::assertEquals($errMessage, $err->expectErr(new Exception('Expected exception message')));
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::unwrapErr
     */
    public function testUnwrapErrReturnsErrorValue(): void
    {
        $errMessage = 'error';
        $err = new Err($errMessage);
        self::assertEquals($errMessage, $err->unwrapErr());
    }

    /**
     * @covers \Prewk\Result\Err::and
     */
    public function testAnd(): void
    {
        $err = new Err('error');
        $result = $err->and(new Ok('ok'));

        self::assertSame($err, $result);
    }

    /**
     * @covers \Prewk\Result\Err::andThen
     */
    public function testAndThen(): void
    {
        $err = new Err('error');
        $result = $err->andThen(static fn () => new Ok('ok'));

        self::assertSame($err, $result);
    }

    /**
     * @covers \Prewk\Result\Err::or
     */
    public function testOr(): void
    {
        $err1 = new Err('error1');
        $err2 = new Err('error2');
        $result = $err1->or($err2);

        self::assertSame($err2, $result);
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::orElse
     */
    public function testOrElseReturnsMappedResult(): void
    {
        $errMessage = 'error';
        $err = new Err($errMessage);
        $newErrorMessage = 'new error';
        $result = $err->orElse(static fn () => new Err($newErrorMessage));
        self::assertInstanceOf(Err::class, $result);
        self::assertEquals($newErrorMessage, $result->err()->unwrap());
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::unwrapOr
     */
    public function testUnwrapOrReturnsDefaultValue(): void
    {
        $defaultValue = 'default';
        $err = new Err('error');
        self::assertEquals($defaultValue, $err->unwrapOr($defaultValue));
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::unwrapOrElse
     */
    public function testUnwrapOrElseReturnsMappedResult(): void
    {
        $errMessage = 'error';
        $err = new Err($errMessage);
        $newErrorMessage = 'new error';
        self::assertEquals($newErrorMessage, $err->unwrapOrElse(static fn () => $newErrorMessage));
    }

    /**
     * @covers \Prewk\Result\Err::__construct
     * @covers \Prewk\Result\Err::apply
     */
    public function testApplyReturnsSelf(): void
    {
        /** @var Err<string> */
        $err = new Err('error');
        $result = $err->apply(new Err('another error'), new Err('yet another error'));
        self::assertSame($err, $result);
    }
}
