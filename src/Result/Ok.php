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
use Prewk\Option;
use Prewk\Option\{Some, None};
use Prewk\Result;

/**
 * Ok
 *
 * @template T
 * The Ok value
 *
 * @template E
 * The Err value
 *
 * @template-extends Result<T, E>
 */
class Ok extends Result
{
    /**
     * @var mixed
     * @psalm-var T
     */
    private $value;

    /**
     * @var array
     */
    private $pass;

    /**
     * Ok constructor.
     *
     * @param mixed $value
     * @psalm-param T $value
     * @param mixed ...$pass
     */
    public function __construct($value, ...$pass)
    {
        $this->value = $value;
        $this->pass = $pass;
    }

    /**
     * @inheritDoc
     */
    public function isOk(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isErr(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function map(Closure $mapper): Result
    {
        return new self($mapper($this->value, ...$this->pass));
    }

    /**
     * @inheritDoc
     */
    public function mapErr(Closure $mapper): Result
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function iter(): array
    {
        return [$this->value];
    }

    /**
     * @inheritDoc
     */
    public function and(Result $res): Result
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
    public function andThen(Closure $op): Result
    {
        $result = $op($this->value, ...$this->pass);

        if (!($result instanceof Result)) {
            throw new ResultException('Op must return a Result');
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function or(Result $res): Result
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orElse(Closure $op): Result
    {
        return $this;
    }

    /**
     * @inheritDoc
     *
     * @psalm-return T
     */
    public function unwrapOr($optb)
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     *
     * @psalm-return T
     */
    public function unwrapOrElse(Closure $op)
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function unwrap()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function expect(Exception $msg)
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     *
     * @psalm-return never-return
     * @throws ResultException if the value is an Ok.
     */
    public function unwrapErr()
    {
        throw new ResultException('Unwrapped with the expectation of Err, but found Ok');
    }

    /**
     * @inheritDoc
     *
     * @throws ResultException
     *
     * @psalm-return Result<array,E>
     *
//     * @psalm-assert T!=callable $this->value
//     * @psalm-assert T!=callable(T=,...mixed=):Result<array,E> $this->value
     * @psalm-suppress MissingClosureReturnType
     */
    public function apply(Result ...$inArgs): Result
    {
        if (!is_callable($this->value)) {
            throw new ResultException('Tried to apply a non-callable to arguments');
        }

        return array_reduce($inArgs, function (Result $final, Result $argResult): Result {
            return $final->andThen(function (array $outArgs) use ($argResult): Result {
                return $argResult->map(function ($unwrappedArg) use ($outArgs): array {
                    $outArgs[] = $unwrappedArg;
                    return $outArgs;
                });
            });
        }, new static([]))
            ->map(function (array $argArray) {
                return call_user_func_array($this->value, $argArray);
            });
    }

    /**
     * Converts from Result<T, E> to Option<T>, and discarding the error, if any
     *
     * @return Option
     * @psalm-return Option<T>
     */
    public function ok(): Option
    {
        return new Some($this->value);
    }

    /**
     * Converts from Result<T, E> to Option<E>, and discarding the value, if any
     *
     * @return Option
     * @psalm-return Option<mixed>
     */
    public function err(): Option
    {
        return new None;
    }

    /**
     * The attached pass-through args will be unpacked into extra args into chained closures
     *
     * @param mixed ...$args
     * @return $this
     */
    public function with(...$args): Result
    {
        $this->pass = $args;

        return $this;
    }
}
