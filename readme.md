# PHP Result Object

A PHP implementation of [Rust's nifty Result type](https://doc.rust-lang.org/std/result/enum.Result.html) with roughly the same API.

## Installation

```php
composer require prewk/result
```

## Usage

```
use Prewk\Result\{Ok, Err};

function someApiCall() {
    // ...
    if ($apiCallSuccesful) {
        return new Ok($results);
    } else {
        return new Err($error);
    }
}

// Fallback
$value = someApiCall()->unwrapOr(null);

// Throw an exception on error
$value = someApiCall()->unwrap();
```