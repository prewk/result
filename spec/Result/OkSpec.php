<?php

namespace spec\Prewk\Result;

use Exception;
use Prewk\Option\{Some, None};
use Prewk\Result\{Ok, Err};
use PhpSpec\ObjectBehavior;
use Prewk\Result\ResultException;

class OkSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith("value");
        $this->shouldHaveType(Ok::class);
    }

    function it_is_ok()
    {
        $this->beConstructedWith("value");
        $this->isOk()->shouldBe(true);
    }

    function it_isnt_err()
    {
        $this->beConstructedWith("value");
        $this->isErr()->shouldBe(false);
    }

    function it_maps()
    {
        $this->beConstructedWith("foo");
        $result = $this->map(function ($value) {
            return $value . "bar";
        });

        $result->shouldHaveType(Ok::class);
        $result->unwrap()->shouldBe("foobar");

        $instance = new class
        {
            public function f($value)
            {
                return $value . "bar";
            }
        };

        $result = $result->map([$instance, 'f']);

        $result->shouldHaveType(Ok::class);
        $result->unwrap()->shouldBe("foobarbar");
    }

    function it_doesnt_mapErr()
    {
        $this->beConstructedWith("foo");
        $result = $this->mapErr(function ($value) {
        });

        $result->shouldHaveType(Ok::class);
        $result->unwrap()->shouldBe("foo");

        $instance = new class
        {
            public function f($value)
            {
            }
        };

        $result = $result->mapErr([$instance, 'f']);

        $result->shouldHaveType(Ok::class);
        $result->unwrap()->shouldBe("foo");
    }

    function it_returns_an_iterator()
    {
        $this->beConstructedWith("foo");
        $this->iter()->shouldBe(["foo"]);
    }

    function it_ands()
    {
        $this->beConstructedWith("foo");
        $this->and(new Ok("bar"))->unwrap()->shouldBe("bar");
    }

    function it_andThens()
    {
        $otherResult = null;

        $this->beConstructedWith("foo");
        $this->andThen(function ($value) use (&$otherResult) {
            $otherResult = new Ok($value . "bar");
            return $otherResult;
        })->shouldBe($otherResult);

        $instance = new class
        {
            public function f($value)
            {
                return new Ok("andThen");
            }
        };
        $this->andThen([$instance, 'f'])->unwrap()->shouldBe("andThen");
    }

    function it_ors()
    {
        $this->beConstructedWith("foo");
        $this->or(new Ok("bar"))->shouldHaveType(Ok::class);
    }

    function it_doesnt_orElse()
    {
        $this->beConstructedWith("foo");
        $this->orElse(function () {
        })->shouldHaveType(Ok::class);

        $instance = new class
        {
            public function f($value)
            {
            }
        };
        $this->orElse([$instance, 'f'])->shouldHaveType(Ok::class);
    }

    function it_unwrapOrs_with_its_value()
    {
        $this->beConstructedWith("value");
        $this->unwrapOr("ignored")->shouldBe("value");
    }

    function it_unwrapOrElses_with_its_value()
    {
        $this->beConstructedWith("value");
        $this->unwrapOrElse(function () {
        })->shouldBe("value");

        $instance = new class
        {
            public function f($value)
            {
            }
        };
        $this->unwrapOrElse([$instance, 'f'])->shouldBe("value");
    }

    function it_unwraps_with_its_value()
    {
        $this->beConstructedWith("value");
        $this->unwrap()->shouldBe("value");
    }

    function it_expects_with_its_value()
    {
        $this->beConstructedWith("value");
        $this->expect(new Exception("ignored"))->shouldBe("value");
    }

    function it_throws_on_unwrapErr()
    {
        $this->beConstructedWith("value");
        $this->shouldThrow(ResultException::class)->during("unwrapErr");
    }

    function it_converts_into_some_with_ok()
    {
        $this->beConstructedWith("value");
        $option = $this->ok();

        $option->shouldHaveType(Some::class);
        $option->unwrap()->shouldBe("value");
    }

    function it_converts_into_none_with_err()
    {
        $this->beConstructedWith("value");
        $option = $this->err();

        $option->shouldHaveType(None::class);
    }

    function it_can_apply_argument_to_function()
    {
        $this->beConstructedWith(function ($one) {
            return $one;
        });
        $arg = new Ok(13);
        $this->apply($arg)->unwrap()->shouldBe(13);
    }

    function it_can_apply_multiple_arguments_to_function()
    {
        $this->beConstructedWith(function ($x, $y, $z) {
            return $x + $y + $z;
        });
        $this->apply(new Ok(1), new Ok(2), new Ok(3))->unwrap()->shouldBe(6);
    }

    function it_returns_err_when_one_of_args_is_err()
    {
        $this->beConstructedWith(function ($x, $y, $z) {
            return $x + $y + $z;
        });
        $this->apply(new Ok(1), new Ok(2), new Err(3))->isErr()->shouldBe(true);
    }

    function it_throws_if_non_callable_value_is_applied_to_arguments()
    {
        $this->beConstructedWith(1);
        $this->shouldThrow(ResultException::class)->during("apply");
    }

    function it_maps_with_pass_args()
    {
        $this->beConstructedWith("foo", "bar", "baz");
        $result = $this->map(function ($foo, $bar, $baz) {
            return $foo . $bar . $baz;
        });

        $result->shouldHaveType(Ok::class);
        $result->unwrap()->shouldBe("foobarbaz");

        $instance = new class
        {
            public function f($foo, $bar, $baz)
            {
                return $foo . $baz . $bar;
            }
        };

        $result = $this->map([$instance, 'f']);
        $result->shouldHaveType(Ok::class);
        $result->unwrap()->shouldBe("foobazbar");
    }

    function it_andThens_with_pass_args()
    {
        $this->beConstructedWith("foo", "bar", "baz");
        $result = $this->andThen(function ($foo, $bar, $baz) {
            return new Ok($foo . $bar . $baz);
        });

        $result->unwrap()->shouldBe("foobarbaz");

        $instance = new class
        {
            public function f($foo, $bar, $baz)
            {
                return new Ok($foo . $baz . $bar);
            }
        };

        $result = $this->andThen([$instance, 'f']);
        $result->unwrap()->shouldBe("foobazbar");
    }

    function its_with_method_adds_args()
    {
        $this->beConstructedWith("foo");
        $this->with("bar", "baz");

        $result = $this->andThen(function ($foo, $bar, $baz) {
            return new Ok($foo . $bar . $baz);
        });

        $result->unwrap()->shouldBe("foobarbaz");
    }
}
