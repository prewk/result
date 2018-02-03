<?php
/**
 * Err
 *
 * Documentation and API borrowed from Rust: https://doc.rust-lang.org/std/result/enum.Result.html
 * @author Oskar Thornblad
 */

declare(strict_types=1);

namespace Prewk\Result;

use Closure;
use Exception;
use Prewk\Option;
use Prewk\Option\{Some, None};
use Prewk\Result;

/**
 * Err
 */
class Err implements Result
{
    /**
     * @var mixed
     */
    private $err;

    /**
     * @var array
     */
    private $pass;

    /**
     * Err constructor.
     *
     * @param mixed $err
     * @param array ...$pass
     */
    public function __construct($err, ...$pass)
    {
        $this->err = $err;
        $this->pass = $pass;
    }

    /**
     * Returns true if the result is Ok.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return false;
    }

    /**
     * Returns true if the result is Err.
     *
     * @return bool
     */
    public function isErr(): bool
    {
        return true;
    }

    /**
     * Maps a Result by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @param Closure $mapper
     * @return Result
     */
    public function map(Closure $mapper): Result
    {
        return $this;
    }

    /**
     * Maps a Result by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @param Closure $mapper
     * @return Result
     */
    public function mapErr(Closure $mapper): Result
    {
        return new self($mapper($this->err, ...$this->pass));
    }

    /**
     * Returns an iterator over the possibly contained value.
     * The iterator yields one value if the result is Ok, otherwise none.
     *
     * @return array
     */
    public function iter(): array
    {
        return [];
    }

    /**
     * Returns res if the result is Ok, otherwise returns the Err value of self.
     *
     * @param Result $res
     * @return Result
     */
    public function and(Result $res): Result
    {
        return $this;
    }

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @param Closure $op
     * @return Result
     */
    public function andThen(Closure $op): Result
    {
        return $this;
    }

    /**
     * Returns res if the result is Err, otherwise returns the Ok value of self.
     *
     * @param Result $res
     * @return Result
     */
    public function or(Result $res): Result
    {
        return $res;
    }

    /**
     * Calls op if the result is Err, otherwise returns the Ok value of self.
     *
     * @param Closure $op T -> Result<T>
     * @return Result
     * @throws ResultException on invalid op return type
     */
    public function orElse(Closure $op): Result
    {
        $result = $op($this->err, ...$this->pass);

        if (!($result instanceof Result)) {
            throw new ResultException("Op must return a Result");
        }

        return $result;
    }

    /**
     * Unwraps a result, yielding the content of an Ok. Else, it returns optb.
     *
     * @param $optb
     * @return mixed
     */
    public function unwrapOr($optb)
    {
        return $optb;
    }

    /**
     * Unwraps a result, yielding the content of an Ok. If the value is an Err then it calls op with its value.
     *
     * @param Closure $op
     * @return mixed
     */
    public function unwrapOrElse(Closure $op)
    {
        return $op($this->err, ...$this->pass);
    }

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @throws Exception|ResultException If the value is an Err, unwrapping will throw it if it's an exception
     *                                   or ResultException if it is not.
     * @return mixed
     */
    public function unwrap()
    {
        if ($this->err instanceof Exception) {
            throw $this->err;
        } else {
            throw new ResultException("Unwrapped an Err");
        }
    }

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @param Exception $msg
     * @return mixed message if the value is an Err.
     * @throws Exception
     */
    public function expect(Exception $msg)
    {
        throw $msg;
    }

    /**
     * Unwraps a result, yielding the content of an Err.
     *
     * @throws if the value is an Ok.
     * @return mixed
     */
    public function unwrapErr()
    {
        return $this->err;
    }
    
    /**
     * Applies values inside the given Results to the function in this Result.
     *
     * @param Result[] ...$args Results to apply the function to.
     * @return Result
     */
    public function apply(Result ...$args): Result
    {
        return $this;
    }

    /**
     * Converts from Result<T, E> to Option<T>, and discarding the error, if any
     *
     * @return Option
     */
    public function ok(): Option
    {
        return new None;
    }

    /**
     * Converts from Result<T, E> to Option<E>, and discarding the value, if any
     *
     * @return Option
     */
    public function err(): Option
    {
        return new Some($this->err);
    }

    /**
     * The attached pass-through args will be unpacked into extra args into chained closures
     *
     * @param array ...$args
     * @return Result
     */
    public function with(...$args): Result
    {
        $this->pass = $args;

        return $this;
    }
}