<?php
/**
 * Procedural style construction of Result instances
 *
 * @author Oskar Thornblad
 */

if (!function_exists("ok")) {
    /**
     * Represent a successful result
     *
     * @codeCoverageIgnore
     *
     * @template T
     *
     * @param mixed $value
     * @psalm-param T $value
     * @param mixed ...$pass
     * @return Prewk\Result\Ok
     * @psalm-return Prewk\Result\Ok<T,mixed>
     */
    function ok($value = null, ...$pass): Prewk\Result\Ok
    {
        return new Prewk\Result\Ok($value, ...$pass);
    }
}

if (!function_exists("err")) {
    /**
     * Represent a failed result
     *
     * @codeCoverageIgnore
     *
     * @template E
     *
     * @param mixed $err
     * @psalm-param E $err
     * @param array ...$pass
     * @return Prewk\Result\Err
     * @psalm-return Prewk\Result\Err<mixed,E>
     */
    function err($err, ...$pass): Prewk\Result\Err
    {
        return new Prewk\Result\Err($err, $pass);
    }
}
