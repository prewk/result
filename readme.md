# PHP Result Object [![Build Status](https://github.com/prewk/result/actions/workflows/test.yml/badge.svg)](https://github.com/prewk/result/actions) [![Coverage Status](https://coveralls.io/repos/github/prewk/result/badge.svg?branch=master)](https://coveralls.io/github/prewk/result?branch=master)

A PHP implementation of [Rust's Result type](https://doc.rust-lang.org/std/result/enum.Result.html) with roughly the same API.

## Version information

Version 4.x.x requires PHP 8.1.0+. Make sure you match the versions for this and the [Option](https://github.com/prewk/option) library if you use both.

## Installation

```php
composer require prewk/result
```

## Overview

```php
use Prewk\Result;
use Prewk\Result\{Ok, Err};

function someApiCall(): Result {
    // ...
    if ($apiCallSuccesful) {
        return new Ok($results);
    } else {
        return new Err($error);
    }
}

function anotherApiCall(): Result {
    // ...
    if ($apiCallSuccesful) {
        return new Ok($results);
    } else {
        return new Err($error);
    }
}

// Fallback to value
$value = someApiCall()->unwrapOr(null);

// Fallback to result and throw an exception if both fail
$value = someApiCall()->orElse(function($err) {
	return anotherApiCall();
})->unwrap();

// Throw custom exception on error
$value = someApiCall()->expect(new Exception("Oh noes!"));
```

## Helpers

Optional global helper functions exist to simplify result object construction:

```php
ok(); // new Prewk\Result\Ok(null);
ok($val); // new Prewk\Result\Ok($val);
err($e); // new Prewk\Result\Err($e);

```

Add the following to your `composer.json`:

```json
{
  "autoload": {
    "files": ["vendor/prewk/result/helpers.php"]
  }
}
```

## API deviations from Rust

### Exceptions

If an Err containing an `Exception` is unwrapped, that Exception will be thrown. Otherwise a generic `ResultException` will be thrown.

## Gotchas

Note that `or` and `and` will be evaluated immediately:

```php
// This will call all three api calls regardless of successes/errors
$this
	->apiCall()
	->or(anotherApiCall())
	->and(thirdApiCall());
```

See `andThen` and `orElse` for lazy evaluation.

## License

MIT & Apache 2.0
