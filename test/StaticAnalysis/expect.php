<?php

declare(strict_types=1);

namespace Prewk\Result\Test\StaticAnalysis;

use Exception;
use Prewk\Result\Err;
use Prewk\Result\Ok;

/**
 * @param Ok<int> $result
 */
function testOkExpect(Ok $result): int
{
    return $result->expect(new Exception('Unexpected error'));
}

/**
 * @param Err<string> $result
 * @throws Exception
 */
function testErrExpect(Err $result): never
{
    $result->expect(new Exception('Unexpected error'));
}

/**
 * @param Ok<int> $result
 * @throws Exception
 */
function testOkExpectErr(Ok $result): never
{
    $result->expectErr(new Exception('Unexpected error'));
}

/**
 * @param Err<string> $result
 */
function testErrExpectErr(Err $result): string
{
    return $result->expectErr(new Exception('Unexpected error'));
}
