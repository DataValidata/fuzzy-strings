<?php

namespace spec\DataValidata\FuzzyStrings;

use DataValidata\FuzzyStrings\StrengthMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StrengthMatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(StrengthMatcher::class);
    }

    function it_ranks_exact_matches_as_highest()
    {
        $this->matches("CIARAN", "CIARAN")->shouldBe(StrengthMatcher::GOLD_STANDARD);
    }

    function it_ranks_case_insensitive_matches_higher_than_typos()
    {
        $this->matches("ciaran", "CIARAN")->shouldBeApproximately(StrengthMatcher::SILVER_STANDARD, 10);
        $this->matches("ciarax", "CIARAN")->shouldBeApproximately(StrengthMatcher::LEAD_STANDARD, 10);
    }
}
