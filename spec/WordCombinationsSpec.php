<?php

namespace spec\DataValidata\FuzzyStrings;

use DataValidata\FuzzyStrings\WordCombinations;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WordCombinationsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WordCombinations::class);
    }

    function it_handles_single_word_strings()
    {
        $this->beConstructedWith("hello");
        $this->getCombinations()->shouldHaveCount((2**1)-1);
    }

    function it_handles_two_word_strings()
    {
        $this->beConstructedWith("hello my");
        $this->getCombinations()->shouldHaveCount((2**2)-1);
    }

    function it_handles_ten_word_strings()
    {
        $this->beConstructedWith("hello my name is ciaran kelly and I'm irish ok");
        $this->getCombinations()->shouldHaveCount((2**10)-1);
    }
}
