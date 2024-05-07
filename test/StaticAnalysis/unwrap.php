<?php

declare(strict_types=1);

namespace Prewk\Result\Test\StaticAnalysis;

use Exception;
use Prewk\Result\Err;
use Prewk\Result\Ok;

/**
 * @param Ok<int> $result
 */
function testOkUnwrap(Ok $result): int
{
    return $result->unwrap();
}

/**
 * @param Err<string> $result
 * @throws Exception
 */
function testErrUnwrap(Err $result): never
{
    $result->unwrap();
}

/**
 * @param Ok<int> $result
 */
function testOkUnwrapErr(Ok $result): never
{
    $result->unwrapErr();
}

/**
 * @param Err<string> $result
 */
function testErrUnwrapErr(Err $result): string
{
    return $result->unwrapErr();
}
