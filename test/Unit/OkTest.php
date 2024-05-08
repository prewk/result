<?php

declare(strict_types=1);

namespace Prewk\Result\Test\Unit;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prewk\Option\None;
use Prewk\Option\Some;
use Prewk\Result\Err;
use Prewk\Result\Ok;
use Prewk\Result\ResultException;
use Stringable;

/**
 * @covers \Prewk\Result\Ok
 * @uses \Prewk\Result\Err
 * @uses \Prewk\Option\None
 * @uses \Prewk\Option\Some
 */
final class OkTest extends TestCase
{
    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::isOk
     */
    public function testIsOkReturnsTrue(): void
    {
        $ok = new Ok('value');
        self::assertTrue($ok->isOk());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::isErr
     */
    public function testIsErrReturnsFalse(): void
    {
        $ok = new Ok('value');
        self::assertFalse($ok->isErr());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::ok
     */
    public function testOkReturnsSomeWithValue(): void
    {
        $value = 'value';
        $ok = new Ok($value);
        self::assertInstanceOf(Some::class, $ok->ok());
        self::assertEquals($value, $ok->ok()->unwrap());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::err
     */
    public function testErrReturnsNone(): void
    {
        $ok = new Ok('value');
        self::assertInstanceOf(None::class, $ok->err());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::map
     */
    public function testMapReturnsMappedResult(): void
    {
        $value = 'value';
        $ok = new Ok($value);
        $newValue = 'new value';
        $result = $ok->map(static fn () => $newValue);
        self::assertInstanceOf(Ok::class, $result);
        self::assertEquals($newValue, $result->ok()->unwrap());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::mapOr
     */
    public function testMapOrReturnsMappedResult(): void
    {
        $value = 'value';
        $ok = new Ok($value);
        $defaultValue = 'default';
        $result = $ok->mapOr($defaultValue, static fn () => 'new value');
        self::assertEquals('new value', $result);
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::mapOrElse
     */
    public function testMapOrElseReturnsMappedResult(): void
    {
        $value = 'value';
        $ok = new Ok($value);
        $defaultValue = 'default';
        $result = $ok->mapOrElse(static fn () => $defaultValue, static fn () => 'new value');
        self::assertEquals('new value', $result);
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::mapErr
     */
    public function testMapErrReturnsSelf(): void
    {
        $ok = new Ok('value');
        $mappedResult = $ok->mapErr(static function (): never {
            // This callback should not execute for an Ok
            self::fail('MapErr callback should not execute for Ok');
        });
        self::assertSame($ok, $mappedResult);
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::inspect
     */
    public function testInspectExecutesCallback(): void
    {
        $value = 'value';
        $ok = new Ok($value);
        $callbackExecuted = false;
        $result = $ok->inspect(static function () use (&$callbackExecuted) {
            $callbackExecuted = true;
        });
        self::assertTrue($callbackExecuted);
        self::assertSame($ok, $result);
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::inspectErr
     */
    public function testInspectErrDoesNotExecuteCallback(): void
    {
        $ok = new Ok('value');
        $result = $ok->inspectErr(static function (): never {
            // This callback should not execute for an Ok
            self::fail('InspectErr callback should not execute for Ok');
        });
        self::assertSame($ok, $result);
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::unwrap
     */
    public function testUnwrapReturnsValue(): void
    {
        $value = 'value';
        $ok = new Ok($value);
        self::assertEquals($value, $ok->unwrap());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::expect
     */
    public function testExpectReturnsValue(): void
    {
        $value = 'value';
        $ok = new Ok($value);
        self::assertEquals($value, $ok->expect(new Exception('Expected exception message')));
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::expectErr
     */
    public function testExpectErrThrowsException(): never
    {
        $ok = new Ok('value');
        $this->expectException(Exception::class);
        $ok->expectErr(new Exception('Expected exception message'));
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::unwrapErr
     */
    public function testUnwrapErrWithThrowable(): never
    {
        $throwable = new InvalidArgumentException('Invalid argument');
        $ok = new Ok($throwable);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid argument');

        $ok->unwrapErr();
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::unwrapErr
     */
    public function testUnwrapErrWithString(): never
    {
        $stringValue = 'Test error';
        $ok = new Ok($stringValue);

        $this->expectException(ResultException::class);
        $this->expectExceptionMessage($stringValue);

        $ok->unwrapErr();
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::unwrapErr
     */
    public function testUnwrapErrWithStringable(): never
    {
        $stringableObject = new class() implements Stringable {
            public function __toString(): string
            {
                return 'Stringable error';
            }
        };
        $ok = new Ok($stringableObject);

        $this->expectException(ResultException::class);
        $this->expectExceptionMessage('Stringable error');

        $ok->unwrapErr();
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::unwrapErr
     */
    public function testUnwrapErrWithNonString(): never
    {
        $nonString = 42;
        $ok = new Ok($nonString);

        $this->expectException(ResultException::class);
        $this->expectExceptionMessage('Unwrapped an Ok');

        $ok->unwrapErr();
    }

    /**
     * @covers \Prewk\Result\Ok::iter
     */
    public function testIterOnOk(): void
    {
        $ok = new Ok(42);
        self::assertEquals([42], $ok->iter());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::and
     */
    public function testAndReturnsResult(): void
    {
        $ok = new Ok('value');
        $result = $ok->and(new Ok('value2'));
        self::assertInstanceOf(Ok::class, $result);
        self::assertEquals('value2', $result->unwrap());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::andThen
     */
    public function testAndThenReturnsMappedResult(): void
    {
        $ok = new Ok('value');
        $result = $ok->andThen(static fn ($v) => new Ok($v . '2'));
        self::assertInstanceOf(Ok::class, $result);
        self::assertEquals('value2', $result->unwrap());
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::or
     */
    public function testOrReturnsSelf(): void
    {
        /** @var Ok<string> */
        $ok = new Ok('value');
        $result = $ok->or(new Ok('value2'));
        self::assertSame($ok, $result);
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::orElse
     */
    public function testOrElseReturnsSelf(): void
    {
        /** @var Ok<string> */
        $ok = new Ok('value');
        $result = $ok->orElse(static fn () => new Ok('value2'));
        self::assertSame($ok, $result);
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::unwrapOr
     */
    public function testUnwrapOrReturnsValue(): void
    {
        /** @var Ok<string> */
        $ok = new Ok('value');
        $defaultValue = 'default';
        self::assertEquals($ok->unwrap(), $ok->unwrapOr($defaultValue));
    }

    /**
     * @covers \Prewk\Result\Ok::__construct
     * @covers \Prewk\Result\Ok::unwrapOrElse
     */
    public function testUnwrapOrElseReturnsValue(): void
    {
        /** @var Ok<string> */
        $ok = new Ok('value');
        $defaultValue = 'default';
        self::assertEquals($ok->unwrap(), $ok->unwrapOrElse(static fn () => $defaultValue));
    }
}
