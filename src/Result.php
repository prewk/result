<?php

/**
 * Result
 *
 * Documentation and API borrowed from Rust: https://doc.rust-lang.org/std/result/enum.Result.html
 * @author Oskar Thornblad
 */

declare(strict_types=1);

namespace Prewk;

use Exception;
use Prewk\Result\Err;
use Prewk\Result\Ok;
use Prewk\Result\ResultException;

/**
 * Describes a Result
 *
 * @template T
 * The Ok value
 *
 * @template E
 * The Err value
 */
abstract class Result
{
    /**
     * Returns true if the result is Ok.
     *
     * @psalm-assert-if-true Ok<T> $this
     * @psalm-assert-if-false Err<E> $this
     */
    abstract public function isOk(): bool;

    /**
     * Returns true if the result is Err.
     *
     * @psalm-assert-if-true Err<E> $this
     * @psalm-assert-if-false Ok<T> $this
     */
    abstract public function isErr(): bool;

    /**
     * Converts from Result<T, E> to Option<T>, and discarding the error, if any
     *
     * @return Option<T>
     */
    abstract public function ok(): Option;

    /**
     * Converts from Result<T, E> to Option<E>, and discarding the value, if any
     *
     * @return Option<E>
     */
    abstract public function err(): Option;

    /**
     * Maps a Result by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @template U
     *
     * @param callable(T=,mixed...):U $mapper
     * @return Result<U,E>
     */
    abstract public function map(callable $mapper): self;

    /**
     * Returns the provided default (if Err), or applies a function to the contained value (if Ok).
     *
     * @template U
     *
     * @param U $default
     * @param callable(T=,mixed...):U $f
     *
     * @return U
     */
    abstract public function mapOr($default, callable $f): mixed;

    /**
     * Maps a Result<T, E> to U by applying fallback function default to a contained Err value, or function f to a contained Ok value.
     *
     * @template U
     *
     * @param callable(E=,mixed...):U $default
     * @param callable(T=,mixed...):U $f
     *
     * @return U
     */
    abstract public function mapOrElse(callable $default, callable $f): mixed;

    /**
     * Maps a Result by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @template F
     *
     * @param callable(E=,mixed...):F $op
     * @return Result<T,F>
     */
    abstract public function mapErr(callable $op): self;

    /**
     * Calls a function with a reference to the contained value if Ok.
     *
     * @param callable(T=,mixed...):void $f
     *
     * @return Result<T,E>
     */
    abstract public function inspect(callable $f): self;

    /**
     * Calls a function with a reference to the contained value if Err.
     *
     * @param callable(E=,mixed...):void $f
     *
     * @return Result<T,E>
     */
    abstract public function inspectErr(callable $f): self;

    /**
     * Returns an iterator over the possibly contained value.
     * The iterator yields one value if the result is Ok, otherwise none.
     *
     * @return iterable<int, T>
     */
    abstract public function iter(): iterable;

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @return T
     * @throws Exception the message if the value is an Err.
     */
    abstract public function expect(Exception $msg): mixed;

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @return T
     * @throws Exception if the value is an Err.
     */
    abstract public function unwrap(): mixed;

    /**
     * Returns the contained Err value, consuming the self value.
     *
     * @return E
     * @throws Exception if the value is an Ok.
     */
    abstract public function expectErr(Exception $msg): mixed;

    /**
     * Unwraps a result, yielding the content of an Err.
     *
     * @return E
     * @throws Exception if the value is an Ok.
     */
    abstract public function unwrapErr(): mixed;

    /**
     * Returns res if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     *
     * @param Result<U,E> $res
     * @return Result<U,E>
     */
    abstract public function and(self $res): self;

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @template U
     *
     * @param callable(T=,mixed...):Result<U,E> $op
     * @return Result<U,E>
     */
    abstract public function andThen(callable $op): self;

    /**
     * Returns res if the result is Err, otherwise returns the Ok value of self.
     *
     * @template F
     *
     * @param Result<T,F> $res
     * @return Result<T,F>
     */
    abstract public function or(self $res): self;

    /**
     * Calls op if the result is Err, otherwise returns the Ok value of self.
     *
     * @template F
     *
     * @param callable(E=,mixed...):Result<T,F> $op
     * @return Result<T,F>
     */
    abstract public function orElse(callable $op): self;

    /**
     * Unwraps a result, yielding the content of an Ok. Else, it returns optb.
     *
     * @param T $optb
     * @return T
     */
    abstract public function unwrapOr($optb): mixed;

    /**
     * Unwraps a result, yielding the content of an Ok. If the value is an Err then it calls op with its value.
     *
     * @param callable(E=,mixed...):T $op
     * @return T
     */
    abstract public function unwrapOrElse(callable $op): mixed;

    /**
     * Applies values inside the given Results to the function in this Result.
     *
     * @param Result<mixed,E> ...$inArgs Results to apply the function to.
     * @return Result<mixed,E>
     *
     * @throws ResultException
     */
    abstract public function apply(self ...$inArgs): self;

    /**
     * The attached pass-through args will be unpacked into extra args into chained callables
     *
     * @return Result<T,E>
     */
    abstract public function with(mixed ...$args): self;
}
