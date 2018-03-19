<?php
/**
 * Result
 *
 * Documentation and API borrowed from Rust: https://doc.rust-lang.org/std/result/enum.Result.html
 * @author Oskar Thornblad
 */

declare(strict_types=1);

namespace Prewk;

use Closure;
use Exception;

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
     * @return bool
     */
    abstract public function isOk(): bool;

    /**
     * Returns true if the result is Err.
     *
     * @return bool
     */
    abstract public function isErr(): bool;

    /**
     * Converts from Result<T, E> to Option<T>, and discarding the error, if any
     *
     * @return Option
     * @psalm-return Option<T>
     */
    abstract public function ok(): Option;

    /**
     * Converts from Result<T, E> to Option<E>, and discarding the value, if any
     *
     * @return Option
     * @psalm-return Option<E>
     */
    abstract public function err(): Option;

    /**
     * Maps a Result by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @param Closure $mapper
     * @return Result
     */
    abstract public function map(Closure $mapper): Result;

    /**
     * Maps a Result by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @param Closure $mapper
     * @return Result
     */
    abstract public function mapErr(Closure $mapper): Result;

    /**
     * Returns an iterator over the possibly contained value.
     * The iterator yields one value if the result is Ok, otherwise none.
     *
     * @return array
     * @psalm-return array<int, mixed>
     */
    abstract public function iter(): array;

    /**
     * Returns res if the result is Ok, otherwise returns the Err value of self.
     *
     * @param Result $res
     * @return Result
     */
    abstract public function and(Result $res): Result;

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @param Closure $op
     * @return Result
     */
    abstract public function andThen(Closure $op): Result;

    /**
     * Returns res if the result is Err, otherwise returns the Ok value of self.
     *
     * @param Result $res
     * @return Result
     */
    abstract public function or(Result $res): Result;

    /**
     * Calls op if the result is Err, otherwise returns the Ok value of self.
     *
     * @param Closure $op
     * @return Result
     */
    abstract public function orElse(Closure $op): Result;

    /**
     * Unwraps a result, yielding the content of an Ok. Else, it returns optb.
     *
     * @param mixed $optb
     * @return mixed
     * @psalm-return T|mixed
     */
    abstract public function unwrapOr($optb);

    /**
     * Unwraps a result, yielding the content of an Ok. If the value is an Err then it calls op with its value.
     *
     * @param Closure $op
     * @return mixed
     * @psalm-return T|mixed
     */
    abstract public function unwrapOrElse(Closure $op);

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @throws if the value is an Err.
     * @return mixed
     * @psalm-return T
     */
    abstract public function unwrap();

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @throws the message if the value is an Err.
     * @param Exception $msg
     * @return mixed
     * @psalm-return T
     */
    abstract public function expect(Exception $msg);

    /**
     * Unwraps a result, yielding the content of an Err.
     *
     * @throws if the value is an Ok.
     * @return mixed
     * @psalm-return E
     */
    abstract public function unwrapErr();

    /**
     * Applies values inside the given Results to the function in this Result.
     *
     * @param Result[] ...$inArgs Results to apply the function to.
     * @psalm-param Result ...$inArgs
     * @return Result
     */
    abstract public function apply(Result ...$inArgs): Result;

    /**
     * The attached pass-through args will be unpacked into extra args into chained closures
     *
     * @param array ...$args
     * @return Result
     */
    abstract public function with(...$args): Result;
}