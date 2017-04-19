# PHP Result Object [![Build Status](https://travis-ci.org/prewk/record.svg)](https://travis-ci.org/prewk/record) [![Coverage Status](https://coveralls.io/repos/github/prewk/result/badge.svg?branch=master)](https://coveralls.io/github/prewk/result?branch=master)

A PHP implementation of [Rust's Result type](https://doc.rust-lang.org/std/result/enum.Result.html) with roughly the same API.

## Installation

```php
composer require prewk/result
```

## Usage

```
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
$value = someApiCall()->or(anotherApiCall())->unwrap();

// Throw custom exception on error
$value = someApiCall()->expect(new Exception("Oh noes!"));
```

## License

MIT & Apache 2.0