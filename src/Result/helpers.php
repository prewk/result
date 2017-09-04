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
     * @return Prewk\Result\Ok
     */
    function ok($value = null): Prewk\Result\Ok {
        return new Prewk\Result\Ok($value);
    }
}

if (!function_exists("err")) {
    /**
     * Represent a failed result
     *
     * @codeCoverageIgnore
     * @param mixed $value
     * @return Prewk\Result\Err
     */
    function err($err): Prewk\Result\Err {
        return new Prewk\Result\Err($err);
    }
}
