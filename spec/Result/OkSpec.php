<?php

namespace spec\Prewk\Result;

use Exception;
use Prewk\Option\{Some, None};
use Prewk\Result\Ok;
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
        $result = $this->map(function($value) {
            return $value . "bar";
        });

        $result->shouldHaveType(Ok::class);
        $result->unwrap()->shouldBe("foobar");
    }

    function it_doesnt_errMap()
    {
        $this->beConstructedWith("foo");
        $result = $this->mapErr(function($value) {});

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
        $this->andThen(function($value) use (&$otherResult) {
            $otherResult = new Ok($value . "bar");
            return $otherResult;
        })->shouldBe($otherResult);
    }

    function it_throws_on_andThen_closure_return_type_mismatch()
    {
        $this->beConstructedWith("foo");
        $this->shouldThrow(ResultException::class)->during("andThen", [function() {
            return "Not a result";
        }]);
    }

    function it_ors()
    {
        $this->beConstructedWith("foo");
        $this->or(new Ok("bar"))->shouldBe($this);
    }

    function it_doesnt_orElse()
    {
        $this->beConstructedWith("foo");
        $this->orElse(function() {})->shouldBe($this);
    }

    function it_unwrapOrs_with_its_value()
    {
        $this->beConstructedWith("value");
        $this->unwrapOr("ignored")->shouldBe("value");
    }

    function it_unwrapOrElses_with_its_value()
    {
        $this->beConstructedWith("value");
        $this->unwrapOrElse(function() {})->shouldBe("value");
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
        $this->beConstructedWith(function($one) {
            return $one;
        });
        $arg = new Ok(13);
        $this->apply($arg)->unwrap()->shouldBe(13);
    }
    
    function it_can_apply_multiple_arguments_to_function()
    {
        $this->beConstructedWith(function($x, $y, $z) {
            return $x + $y + $z;
        });
        $this->apply(new Ok(1), new Ok(2), new Ok(3))->unwrap()->shouldBe(6);
    }
}
