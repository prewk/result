<?php

namespace spec\Prewk\Result;

use Exception;
use Prewk\Option\{Some, None};
use Prewk\Result\Err;
use PhpSpec\ObjectBehavior;
use Prewk\Result\Ok;
use Prewk\Result\ResultException;

class ErrSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith("error");
        $this->shouldHaveType(Err::class);
    }

    function it_isnt_ok()
    {
        $this->beConstructedWith("error");
        $this->isOk()->shouldBe(false);
    }

    function it_is_err()
    {
        $this->beConstructedWith("error");
        $this->isErr()->shouldBe(true);
    }

    function it_doesnt_map()
    {
        $this->beConstructedWith("error");
        $this->map(function () {
        })->shouldHaveType(Err::class);

        $instance = new class
        {
            public function f($value)
            {
            }
        };
        $this->map([$instance, 'f'])->shouldHaveType(Err::class);
    }

    function it_mapErrs()
    {
        $this->beConstructedWith("foo");
        $result = $this->mapErr(function ($err) {
            return $err . "bar";
        });

        $result->shouldHaveType(Err::class);
        $result->unwrapErr()->shouldBe("foobar");

        $instance = new class
        {
            public function f($err)
            {
                return $err . "baz";
            }
        };

        $result = $this->mapErr([$instance, 'f']);
        $result->shouldHaveType(Err::class);
        $result->unwrapErr()->shouldBe("foobaz");
    }

    function it_returns_an_iterator()
    {
        $this->beConstructedWith("error");
        $this->iter()->shouldBe([]);
    }

    function it_shouldnt_and()
    {
        $this->beConstructedWith("error");
        $this->and(new Err("ignored"))->shouldHaveType(Err::class);
    }

    function it_shouldnt_andThen()
    {
        $this->beConstructedWith("error");
        $this->andThen(function () {
        })->shouldHaveType(Err::class);

        $instance = new class
        {
            public function f($err)
            {
            }
        };
        $this->andThen([$instance, 'f'])->shouldHaveType(Err::class);
    }

    function it_should_or()
    {
        $fallback = new Ok("value");

        $this->beConstructedWith("error");
        $this->or($fallback)->shouldBe($fallback);
    }

    function it_should_orElse()
    {
        $otherValue = null;

        $this->beConstructedWith("error");
        $this->orElse(function ($err) use (&$otherValue) {
            $otherValue = new Err($err . "rorre");
            return $otherValue;
        })->shouldBe($otherValue);

        $instance = new class
        {
            public function f($err)
            {
                return new Err($err . "baz");
            }
        };
        $this->orElse([$instance, 'f'])->unwrapErr()->shouldBe("errorbaz");
    }

    function it_unwrapOrs()
    {
        $this->beConstructedWith("error");
        $this->unwrapOr("valid")->shouldBe("valid");
    }

    function it_unwrapOrElses()
    {
        $this->beConstructedWith("error");
        $this->unwrapOrElse(function ($err) {
            return "non-" . $err;
        })->shouldBe("non-error");

        $instance = new class
        {
            public function f($err)
            {
                return "non-" . $err;
            }
        };
        $this->unwrapOrElse([$instance, 'f'])->shouldBe("non-error");
    }

    function it_throws_ResultException_on_unwrapping_non_exceptions()
    {
        $this->beConstructedWith("error");
        $this->shouldThrow(ResultException::class)->during("unwrap");
    }

    function it_throws_the_err_on_unwrapping_exception()
    {
        $e = new Exception("error");
        $this->beConstructedWith($e);
        $this->shouldThrow($e)->during("unwrap");
    }

    function it_throws_on_expect()
    {
        $msg = new Exception("error");
        $this->beConstructedWith("error");
        $this->shouldThrow($msg)->during("expect", [$msg]);
    }

    function it_unwrapErrs()
    {
        $this->beConstructedWith("error");
        $this->unwrapErr()->shouldBe("error");
    }

    function it_converts_into_none_with_ok()
    {
        $this->beConstructedWith("error");
        $option = $this->ok();

        $option->shouldHaveType(None::class);
    }

    function it_converts_into_some_with_err()
    {
        $this->beConstructedWith("error");
        $option = $this->err();

        $option->shouldHaveType(Some::class);
        $option->unwrap()->shouldBe("error");
    }

    function it_does_not_apply_args_in_an_err()
    {
        $this->beConstructedWith("error");
        $this->apply(new Ok(123))->isErr()->shouldBe(true);
    }

    function it_mapErrs_with_pass_args()
    {
        $this->beConstructedWith("foo", "bar", "baz");
        $result = $this->mapErr(function ($foo, $bar, $baz) {
            return $foo . $bar . $baz;
        });

        $result->shouldHaveType(Err::class);
        $result->unwrapErr()->shouldBe("foobarbaz");

        $instance = new class
        {
            public function f($foo, $bar, $baz)
            {
                return $foo . $baz . $bar;
            }
        };

        $result = $this->mapErr([$instance, 'f']);
        $result->shouldHaveType(Err::class);
        $result->unwrapErr()->shouldBe("foobazbar");
    }

    function it_orElses_with_pass_args()
    {
        $this->beConstructedWith("foo", "bar", "baz");
        $result = $this->orElse(function ($foo, $bar, $baz) {
            return new Err($foo . $bar . $baz);
        });

        $result->unwrapErr()->shouldBe("foobarbaz");

        $instance = new class
        {
            public function f($foo, $bar, $baz)
            {
                return new Err($foo . $baz . $bar);
            }
        };
        $result = $this->orElse([$instance, 'f']);
        $result->unwrapErr()->shouldBe("foobazbar");
    }

    function its_with_method_adds_args()
    {
        $this->beConstructedWith("foo");
        $this->with("bar", "baz");

        $result = $this->orElse(function ($foo, $bar, $baz) {
            return new Err($foo . $bar . $baz);
        });

        $result->unwrapErr()->shouldBe("foobarbaz");
    }
}
