<?php

declare(strict_types=1);

namespace Prewk\Result\Test\StaticAnalysis;

use Prewk\Result;
use Prewk\Result\Err;
use Prewk\Result\Ok;
use RuntimeException;

function testResultIsOk(Result $result): Ok
{
    if ($result->isOk()) {
        return $result;
    }

    throw new RuntimeException('Expected Ok, got Err');
}

function testResultIsErr(Result $result): Err
{
    if ($result->isErr()) {
        return $result;
    }

    throw new RuntimeException('Expected Err, got Ok');
}

/**
 * @return Result<string, int>
 */
function testResultTypesCanBeComposedFromOkAndErrTypes(): Result
{
    if (random_int(1, 2) === 1) {
        return new Err(1);
    }

    return new Ok('value');
}
