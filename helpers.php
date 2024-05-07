<?php

use Prewk\Result\Err;
use Prewk\Result\Ok;

/**
 * Procedural style construction of Result instances
 *
 * @author Oskar Thornblad
 */

if (! function_exists('ok')) {
    /**
     * Represent a successful result
     *
     * @codeCoverageIgnore
     *
     * @template T
     *
     * @param T $value
     * @param mixed ...$pass
     * @return \Prewk\Result\Ok<T>
     */
    function ok($value = null, ...$pass): Ok
    {
        return new Ok($value, ...$pass);
    }
}

if (! function_exists('err')) {
    /**
     * Represent a failed result
     *
     * @codeCoverageIgnore
     *
     * @template E
     *
     * @param E $err
     * @param array ...$pass
     * @return \Prewk\Result\Err<E>
     */
    function err($err, ...$pass): Err
    {
        return new Err($err, $pass);
    }
}
