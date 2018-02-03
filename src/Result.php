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
 */
interface Result
{
    /**
     * Returns true if the result is Ok.
     *
     * @return bool
     */
    public function isOk(): bool;

    /**
     * Returns true if the result is Err.
     *
     * @return bool
     */
    public function isErr(): bool;

    /**
     * Converts from Result<T, E> to Option<T>, and discarding the error, if any
     *
     * @return Option
     */
    public function ok(): Option;

    /**
     * Converts from Result<T, E> to Option<E>, and discarding the value, if any
     *
     * @return Option
     */
    public function err(): Option;

    /**
     * Maps a Result by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @param Closure $mapper
     * @return Result
     */
    public function map(Closure $mapper): Result;

    /**
     * Maps a Result by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @param Closure $mapper
     * @return Result
     */
    public function mapErr(Closure $mapper): Result;

    /**
     * Returns an iterator over the possibly contained value.
     * The iterator yields one value if the result is Ok, otherwise none.
     *
     * @return array
     */
    public function iter(): array;

    /**
     * Returns res if the result is Ok, otherwise returns the Err value of self.
     *
     * @param Result $res
     * @return Result
     */
    public function and(Result $res): Result;

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @param Closure $op
     * @return Result
     */
    public function andThen(Closure $op): Result;

    /**
     * Returns res if the result is Err, otherwise returns the Ok value of self.
     *
     * @param Result $res
     * @return Result
     */
    public function or(Result $res): Result;

    /**
     * Calls op if the result is Err, otherwise returns the Ok value of self.
     *
     * @param Closure $op
     * @return Result
     */
    public function orElse(Closure $op): Result;

    /**
     * Unwraps a result, yielding the content of an Ok. Else, it returns optb.
     *
     * @param mixed $optb
     * @return mixed
     */
    public function unwrapOr($optb);

    /**
     * Unwraps a result, yielding the content of an Ok. If the value is an Err then it calls op with its value.
     *
     * @param Closure $op
     * @return mixed
     */
    public function unwrapOrElse(Closure $op);

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @throws if the value is an Err.
     * @return mixed
     */
    public function unwrap();

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @throws the message if the value is an Err.
     * @param Exception $msg
     * @return mixed
     */
    public function expect(Exception $msg);

    /**
     * Unwraps a result, yielding the content of an Err.
     *
     * @throws if the value is an Ok.
     * @return mixed
     */
    public function unwrapErr();

    /**
     * Applies values inside the given Results to the function in this Result.
     *
     * @param Result[] ...$args Results to apply the function to.
     * @return Result
     */
    public function apply(Result ...$args): Result;

    /**
     * The attached pass-through args will be unpacked into extra args into chained closures
     *
     * @param array ...$args
     * @return Result
     */
    public function with(...$args): Result;
}