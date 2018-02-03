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
     * @param mixed $value
     * @param array ...$pass
     * @return Prewk\Result\Ok
     */
    function ok($value = null, ...$pass): Prewk\Result\Ok {
        return new Prewk\Result\Ok($value, ...$pass);
    }
}

if (!function_exists("err")) {
    /**
     * Represent a failed result
     *
     * @codeCoverageIgnore
     * @param $err
     * @param array ...$pass
     * @return Prewk\Result\Err
     */
    function err($err, ...$pass): Prewk\Result\Err {
        return new Prewk\Result\Err($err, $pass);
    }
}
