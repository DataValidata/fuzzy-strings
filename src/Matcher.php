<?php

namespace DataValidata\FuzzyStrings;

class Matcher
{
    private $caseSensitive = true;
    private $forceInexact = false;

    private function cloneMe()
    {
        $newInstance = new static();
        $newInstance->caseSensitive = $this->caseSensitive;
        $newInstance->forceInexact = $this->forceInexact;
        return $newInstance;
    }

    public function caseInsensitive()
    {
        $newInstance = $this->cloneMe();
        $newInstance->caseSensitive = false;
        return $newInstance;
    }

    public function inexactly()
    {
        $newInstance = $this->cloneMe();
        $newInstance->forceInexact = true;
        return $newInstance;
    }

    public function matches($needle, $haystack, $threshold = 0)
    {
        $minimumEditsRequired = $this->minimumEditsRequired($needle, $haystack);
        if($this->forceInexact && $minimumEditsRequired === 0) {
            return false;
        }

        if($minimumEditsRequired === strlen($needle)) {
            return false;
        }

        return $minimumEditsRequired <= $threshold;
    }

    /**
     * @param $needle
     * @param $haystack
     * @return mixed
     */
    private function minimumEditsRequired($needle, $haystack)
    {
        $needleLength   = strlen($needle);
        $haystackLength = strlen($haystack);

        // short circuit best case scenarios
        if($needleLength == 1) {
            /**
             * needle is only one character long, so, either it is in the haystack,
             * meaning no edits are required, or, it is not, meaning a single edit
             * would be required.
             */
            if(!$this->caseSensitive) {
                $needle = strtolower($needle);
                $haystack = strtolower($haystack);
            }
            return (int) !(strstr($haystack, $needle));
        }

        if(!$haystackLength) {
            /**
             * an empty haystack will require needleLength edits to match
             */
            return $needleLength;
        }

        return min($this->calculateBottomRowOfLevenshteinTable($needle, $haystack));
    }

    /**
     * Algorithm ported from:
     *      http://ginstrom.com/scribbles/2007/12/01/fuzzy-substring-matching-with-levenshtein-distance-in-python/
     * @param $needle
     * @param $haystack
     * @return array
     */
    private function calculateBottomRowOfLevenshteinTable($needle, $haystack)
    {
        if(!$this->caseSensitive) {
            $needle = strtolower($needle);
            $haystack = strtolower($haystack);
        }

        $m = strlen($needle);
        $n = strlen($haystack);
        $row1 = array_fill(0, $n + 2, 0);  // initialise first row to zeros
        foreach (range(0, $m - 1) as $i) {
            $row2 = [$i + 1];
            foreach (range(0, $n - 1) as $j) {
                $cost = ($needle{$i} != $haystack{$j});

                $possibleEdits = [
                    $row1[$j + 1] + 1,     // deletion
                    $row2[$j] + 1,         // insertion
                    $row1[$j] + $cost,     // substitution
                ];
                array_push($row2, min($possibleEdits));
            }
            $row1 = $row2;
        }
        return $row1;
    }
}
