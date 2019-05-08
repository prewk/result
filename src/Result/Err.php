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
 *
 * @template T
 * The Ok value
 *
 * @template E
 * The Err value
 *
 * @template-extends Result<T, E>
 */
class Err extends Result
{
    /**
     * @var mixed
     * @psalm-var E
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
     * @psalm-param E $err
     * @param array ...$pass
     */
    public function __construct($err, ...$pass)
    {
        $this->err = $err;
        $this->pass = $pass;
    }

    /**
     * @inheritDoc
     */
    public function isOk(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isErr(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     *
     * @return $this
     */
    public function map(Closure $mapper): Result
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function mapErr(Closure $mapper): Result
    {
        return new self($mapper($this->err, ...$this->pass));
    }

    /**
     * @inheritDoc
     */
    public function iter(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     *
     * @return $this
     */
    public function and(Result $res): Result
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function andThen(Closure $op): Result
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function or(Result $res): Result
    {
        return $res;
    }

    /**
     * @inheritDoc
     *
     * @throws ResultException on invalid op return type
     * @psalm-assert !Closure(T=):Result $op
     *
     * @psalm-suppress DocblockTypeContradiction We cannot be completely sure, that in argument valid callable
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
     * @inheritDoc
     *
     * @template U
     *
     * @psalm-return U
     */
    public function unwrapOr($optb)
    {
        return $optb;
    }

    /**
     * @inheritDoc
     *
     * @template U
     *
     * @psalm-return U
     */
    public function unwrapOrElse(Closure $op)
    {
        return $op($this->err, ...$this->pass);
    }

    /**
     * @inheritDoc
     *
     * @psalm-return never-return
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
     * @inheritDoc
     *
     * @psalm-return never-return
     */
    public function expect(Exception $msg)
    {
        throw $msg;
    }

    /**
     * @inheritDoc
     */
    public function unwrapErr()
    {
        return $this->err;
    }

    /**
     * @inheritDoc
     *
     * @return $this
     */
    public function apply(Result ...$inArgs): Result
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function ok(): Option
    {
        return new None;
    }

    /**
     * @inheritDoc
     */
    public function err(): Option
    {
        return new Some($this->err);
    }

    /**
     * @inheritDoc
     *
     * @return $this
     */
    public function with(...$args): Result
    {
        $this->pass = $args;

        return $this;
    }
}
