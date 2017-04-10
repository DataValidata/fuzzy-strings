<?php

namespace DataValidata\FuzzyStrings;

class StrengthMatcher
{
    const CASE_PENALTY = 300;

    const GOLD_STANDARD_MATCHER = 'gold-standard-matcher';
    const GOLD_STANDARD = 9999999;

    const SILVER_STANDARD_MATCHER = 'silver-standard-matcher';
    const SILVER_STANDARD = 9000;

    const BRONZE_STANDARD_MATCHER = 'bronze-standard-matcher';
    const BRONZE_STANDARD = 1000;

    const LEAD_STANDARD_MATCHER = 'lead-standard-matcher';
    const LEAD_STANDARD = 500;

    const NO_MATCH = null;

    private $fuzzyMatchers = [];

    public function __construct()
    {
        $fuzzyMatcher = new Matcher();

        $caseInsensitiveFuzzyMatcher = $fuzzyMatcher->caseInsensitive();

        // exact match
        $this->fuzzyMatchers[] = [
            'name' => self::GOLD_STANDARD_MATCHER,
            'matcher' => function($str1, $str2) {
                return (strcmp($str1, $str2) === 0);
            },
            'scorer' => function($str1, $str2) {
                return self::GOLD_STANDARD;
            }
        ];

        // exact, ignoring case
        $this->fuzzyMatchers[] = [
            'name' => self::SILVER_STANDARD_MATCHER,
            'matcher' => function($str1, $str2) {
                return (strcasecmp($str1, $str2) === 0);
            },
            'scorer' => function($str1, $str2) use($caseInsensitiveFuzzyMatcher){
                return self::SILVER_STANDARD
                       - $caseInsensitiveFuzzyMatcher->distance($str1, $str2)
                    ;
            }
        ];

        // proper substring, case sensitive
        $this->fuzzyMatchers[] = [
            'name' => self::BRONZE_STANDARD_MATCHER,
            'matcher' => function($str1, $str2) {
                return strstr($str2, $str1);
            },
            'scorer' => function($str1, $str2) use($fuzzyMatcher){
                return self::BRONZE_STANDARD
                       - $fuzzyMatcher->distance($str1, $str2)
                    ;
            }
        ];

        // proper substring, case insensitive
        $this->fuzzyMatchers[] = [
            'name' => self::BRONZE_STANDARD_MATCHER,
            'matcher' => function($str1, $str2) {
                return stristr($str2, $str1);
            },
            'scorer' => function($str1, $str2) use($caseInsensitiveFuzzyMatcher){
                return self::BRONZE_STANDARD
                       - self::CASE_PENALTY
                       - $caseInsensitiveFuzzyMatcher->distance($str1, $str2)
                    ;
            }
        ];

        // needs transforms
        $this->fuzzyMatchers[] = [
            'name' => self::LEAD_STANDARD_MATCHER,
            'matcher' => function($str1, $str2) use($fuzzyMatcher) {
                return $fuzzyMatcher->matches($str1, $str2, strlen($str1) / 2);
            },
            'scorer' => function($str1, $str2) use($fuzzyMatcher){
                return self::LEAD_STANDARD
                    - $fuzzyMatcher->distance($str1, $str2)
                    ;
            }
        ];

        // needs transforms
        $this->fuzzyMatchers[] = [
            'name' => self::LEAD_STANDARD_MATCHER,
            'matcher' => function($str1, $str2) use($caseInsensitiveFuzzyMatcher) {
                return $caseInsensitiveFuzzyMatcher->matches($str1, $str2, strlen($str1) / 2);
            },
            'scorer' => function($str1, $str2) use($caseInsensitiveFuzzyMatcher){
                return self::LEAD_STANDARD
                    - $caseInsensitiveFuzzyMatcher->distance($str1, $str2)
                    ;
            }
        ];

        // needs transforms
        $this->fuzzyMatchers[] = [
            'name' => self::LEAD_STANDARD_MATCHER,
            'matcher' => function($str1, $str2) {
                return true;
            },
            'scorer' => function($str1, $str2) {
                return self::LEAD_STANDARD
                    - self::CASE_PENALTY
                    - strlen($str1)
                    - strlen($str2)
                    ;
            }
        ];
    }

    public function matches($string1, $string2)
    {
        $matchFunction = function($str1, $str2, $fuzzyMatcher) {
            $matcher = $fuzzyMatcher['matcher'];
            $scorer = $fuzzyMatcher['scorer'];
            if($matcher($str1, $str2)) {
                return $scorer($str1, $str2);
            }

            return null;
        };

        foreach ($this->fuzzyMatchers as $fuzzyMatcher ) {
            if(($score = $matchFunction($string1, $string2, $fuzzyMatcher)) !== null) {
                return $score;
            }
        }

        return self::NO_MATCH;
    }
}
