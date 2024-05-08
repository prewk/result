<?php

/**
 * Err
 *
 * Documentation and API borrowed from Rust: https://doc.rust-lang.org/std/result/enum.Result.html
 * @author Oskar Thornblad
 */

declare(strict_types=1);

namespace Prewk\Result;

use Exception;
use Prewk\Option;
use Prewk\Option\{None, Some};
use Prewk\Result;
use Throwable;

/**
 * Err
 *
 * @template E
 * The Err value
 *
 * @extends Result<mixed, E>
 */
class Err extends Result
{
    public function __construct(
        /** @var E */
        private $err,
    ) {
    }

    /**
     * @return false
     */
    public function isOk(): bool
    {
        return false;
    }

    /**
     * @return true
     */
    public function isErr(): bool
    {
        return true;
    }

    public function ok(): Option
    {
        return new None();
    }

    public function err(): Option
    {
        return new Some($this->err);
    }

    public function map(callable $mapper): Result
    {
        return $this;
    }

    public function mapOr($default, callable $f): mixed
    {
        return $default;
    }

    public function mapOrElse(callable $default, callable $f): mixed
    {
        return $default($this->err);
    }

    public function mapErr(callable $op): Result
    {
        return new self($op($this->err));
    }

    public function inspect(callable $f): Result
    {
        return $this;
    }

    public function inspectErr(callable $f): Result
    {
        $f($this->err);

        return $this;
    }

    public function iter(): iterable
    {
        return [];
    }

    /**
     * @throws Exception
     */
    public function expect(Exception $msg): never
    {
        throw $msg;
    }

    /**
     * @throws Throwable
     */
    public function unwrap(): never
    {
        if ($this->err instanceof Throwable) {
            throw $this->err;
        }

        throw new ResultException('Unwrapped an Err');
    }

    /**
     * @return E
     */
    public function expectErr(Exception $msg): mixed
    {
        return $this->err;
    }

    /**
     * @return E
     */
    public function unwrapErr(): mixed
    {
        return $this->err;
    }

    public function and(Result $res): Result
    {
        return $this;
    }

    public function andThen(callable $op): Result
    {
        return $this;
    }

    public function or(Result $res): Result
    {
        return $res;
    }

    public function orElse(callable $op): Result
    {
        return $op($this->err);
    }

    public function unwrapOr($optb): mixed
    {
        return $optb;
    }

    public function unwrapOrElse(callable $op): mixed
    {
        return $op($this->err);
    }
}
