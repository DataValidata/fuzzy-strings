<?php

namespace DataValidata\FuzzyStrings;

use Traversable;

class WordCombinations implements \IteratorAggregate
{
    private $string;
    public function __construct($string = '')
    {
        $this->string = $string;
    }

    public function getIterator()
    {
        $words = get_words($this->string);

        $num = count($words);
        //The total number of possible combinations
        $total = pow(2, $num);
        //Loop through each possible combination
        $combos = [];
        for ($i = 0; $i < $total; $i++) {
            $combo = [];
            //For each combination check if each bit is set
            for ($j = 0; $j < $num; $j++) {
                //Is bit $j set in $i?
                if (pow(2, $j) & $i) {
                    $combo[] = $words[$j];
                }
            }
            if(!empty($combo)) {
                yield $combo;
            }
        }
    }


    public function getCombinations()
    {
        return iterator_to_array($this);
    }
}
