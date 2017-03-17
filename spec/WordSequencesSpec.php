<?php

namespace spec\DataValidata\FuzzyStrings;

use DataValidata\FuzzyStrings\WordSequences;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WordSequencesSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(WordSequences::class);
    }

    function it_handles_single_word_strings()
    {
        $this->beConstructedWith("hello");
        $this->getSequences()->shouldHaveCount(1);
    }

    function it_handles_two_word_strings()
    {
        $this->beConstructedWith("hello my");
        $this->getSequences()->shouldHaveCount(2);
    }

    function it_handles_ten_word_strings()
    {
        $this->beConstructedWith("hello my name is ciaran kelly and I'm irish ok");
        $this->getSequences()->shouldHaveCount(10);
        $this->getSequences()->shouldHaveKeyWithValue(0, ["hello"]);
    }

    function it_handles_ten_word_strings_in_reverse()
    {
        $this->beConstructedWith("hello my name is ciaran kelly and I'm irish ok", false);
        $this->getSequences()->shouldHaveCount(10);
        $this->getSequences()->shouldHaveKeyWithValue(0, ["ok"]);
        $this->getSequences()->shouldHaveKeyWithValue(1, ["irish", "ok"]);
    }
}
