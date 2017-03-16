<?php

namespace spec\DataValidata\FuzzyStrings;

use DataValidata\FuzzyStrings\Matcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Matcher::class);
    }

    function it_does_exact_matching_by_default()
    {
        $this->matches("needle", "needle")->shouldBe(true);
        $this->matches("needle", "NEEDLE")->shouldBe(false);
    }

    function it_allows_case_insensitive_matching()
    {
        $this->caseInsensitive()->matches("needle", "needle")->shouldBe(true);
        $this->caseInsensitive()->matches("needle", "NEEDLE")->shouldBe(true);
    }

    function it_allows_a_threshold_to_be_declared()
    {
        $this->matches("NeEdLE", "can you find the need in this haystack?", 3)->shouldBe(false);
        $this->matches("NeEdLE", "can you find the needle in this haystack?", 4)->shouldBe(true);

        $this->caseInsensitive()->matches("needle", "can you find the need in this haystack?", 1)->shouldBe(false);
        $this->caseInsensitive()->matches("NeEdLE", "can you find the needle in this haystack?", 1)->shouldBe(true);
    }

    function it_allows_forced_searches_for_inexact_matches()
    {
        $this->inexactly()->matches("needle", "can you find the needle in this haystack?", 3)->shouldBe(false);
        $this->inexactly()->caseInsensitive()->matches("needle", "can you find the NEEDLE in this haystack?", 3)->shouldBe(false);
        $this->inexactly()->matches("needle", "can you find the needl in this haystack?", 3)->shouldBe(true);
        $this->inexactly()->matches("needle", "can you find the nee in this haystack?", 3)->shouldBe(true);
        $this->inexactly()->matches("needle", "can you find the ne in this haystack?", 3)->shouldBe(false);
    }
}
