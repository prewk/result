<?php

/**
 * Ok
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
use Stringable;
use Throwable;
use function array_reduce;
use function is_string;

/**
 * Ok
 *
 * @template T
 * The Ok value
 *
 * @extends Result<T, mixed>
 */
class Ok extends Result
{
    public function __construct(
        /** @var T */
        private $value,
    ) {
    }

    /**
     * @return true
     */
    public function isOk(): bool
    {
        return true;
    }

    /**
     * @return false
     */
    public function isErr(): bool
    {
        return false;
    }

    public function ok(): Option
    {
        return new Some($this->value);
    }

    public function err(): Option
    {
        return new None();
    }

    public function map(callable $mapper): Result
    {
        return new self($mapper($this->value));
    }

    public function mapOr($default, callable $f): mixed
    {
        return $f($this->value);
    }

    public function mapOrElse(callable $default, callable $f): mixed
    {
        return $f($this->value);
    }

    public function mapErr(callable $op): Result
    {
        return $this;
    }

    public function inspect(callable $f): Result
    {
        $f($this->value);

        return $this;
    }

    public function inspectErr(callable $f): Result
    {
        return $this;
    }

    public function iter(): iterable
    {
        return [$this->value];
    }

    /**
     * @return T
     */
    public function expect(Exception $msg): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->value;
    }

    public function expectErr(Exception $msg): never
    {
        throw $msg;
    }

    public function unwrapErr(): never
    {
        if ($this->value instanceof Throwable) {
            throw $this->value;
        }

        if (is_string($this->value) || $this->value instanceof Stringable) {
            throw new ResultException((string) $this->value);
        }

        throw new ResultException('Unwrapped an Ok');
    }

    public function and(Result $res): Result
    {
        return $res;
    }

    public function andThen(callable $op): Result
    {
        return $op($this->value);
    }

    public function or(Result $res): Result
    {
        return $this;
    }

    public function orElse(callable $op): Result
    {
        return $this;
    }

    public function unwrapOr($optb): mixed
    {
        return $this->value;
    }

    public function unwrapOrElse(callable $op): mixed
    {
        return $this->value;
    }

    public function apply(Result ...$inArgs): Result
    {
        if (! is_callable($this->value)) {
            throw new ResultException('Tried to apply a non-callable to arguments');
        }

        return array_reduce(
            $inArgs,
            static fn (Result $final, Result $argResult): Result => $final->andThen(
                static fn (array $outArgs): Result => $argResult->map(static function (mixed $unwrappedArg) use (
                    $outArgs
                ): array {
                    $outArgs[] = $unwrappedArg;

                    return $outArgs;
                })
            ),
            new self([])
        )
            ->map(fn (array $argArray): mixed => ($this->value)(...$argArray));
    }
}
