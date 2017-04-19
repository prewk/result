<?php
/**
 * Ok
 *
 * Documentation and API borrowed from Rust: https://doc.rust-lang.org/std/result/enum.Result.html
 * @author Oskar Thornblad
 */

declare(strict_types=1);

namespace Prewk\Result;

use Closure;
use Exception;
use Prewk\Result;

/**
 * Ok
 */
class Ok implements Result
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * Ok constructor.
     *
     * @param mixed $value
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * Returns true if the result is Ok.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return true;
    }

    /**
     * Returns true if the result is Err.
     *
     * @return bool
     */
    public function isErr(): bool
    {
        return false;
    }

    /**
     * Maps a Result by applying a function to a contained Ok value, leaving an Err value untouched.
     *
     * @param Closure $mapper T -> T
     * @return Result
     */
    public function map(Closure $mapper): Result
    {
        return new self($mapper($this->value));
    }

    /**
     * Maps a Result by applying a function to a contained Err value, leaving an Ok value untouched.
     *
     * @param Closure $mapper
     * @return Result
     */
    public function mapErr(Closure $mapper): Result
    {
        return $this;
    }

    /**
     * Returns an iterator over the possibly contained value.
     * The iterator yields one value if the result is Ok, otherwise none.
     *
     * @return array
     */
    public function iter(): array
    {
        return [$this->value];
    }

    /**
     * Returns res if the result is Ok, otherwise returns the Err value of self.
     *
     * @param Result $res
     * @return Result
     */
    public function and(Result $res): Result
    {
        return $res;
    }

    /**
     * Calls op if the result is Ok, otherwise returns the Err value of self.
     *
     * @param Closure $op T -> Result<T>
     * @return Result
     * @throws ResultException on invalid op return type
     */
    public function andThen(Closure $op): Result
    {
        $result = $op($this->value);

        if (!($result instanceof Result)) {
            throw new ResultException("Op must return a Result");
        }

        return $result;
    }

    /**
     * Returns res if the result is Err, otherwise returns the Ok value of self.
     *
     * @param Result $res
     * @return Result
     */
    public function or(Result $res): Result
    {
        return $this;
    }

    /**
     * Calls op if the result is Err, otherwise returns the Ok value of self.
     *
     * @param Closure $op
     * @return Result
     */
    public function orElse(Closure $op): Result
    {
        return $this;
    }

    /**
     * Unwraps a result, yielding the content of an Ok. Else, it returns optb.
     *
     * @param $optb
     * @return mixed
     */
    public function unwrapOr($optb)
    {
        return $this->value;
    }

    /**
     * Unwraps a result, yielding the content of an Ok. If the value is an Err then it calls op with its value.
     *
     * @param Closure $op
     * @return mixed
     */
    public function unwrapOrElse(Closure $op)
    {
        return $this->value;
    }

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @throws if the value is an Err.
     * @return mixed
     */
    public function unwrap()
    {
        return $this->value;
    }

    /**
     * Unwraps a result, yielding the content of an Ok.
     *
     * @throws the message if the value is an Err.
     * @param Exception $msg
     * @return mixed
     */
    public function expect(Exception $msg)
    {
        return $this->value;
    }

    /**
     * Unwraps a result, yielding the content of an Err.
     *
     * @throws ResultException if the value is an Ok.
     * @return mixed
     */
    public function unwrapErr()
    {
        throw new ResultException("Unwrapped with the expecation of Err, but found Ok");
    }
}