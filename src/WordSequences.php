<?php

namespace DataValidata\FuzzyStrings;

use Traversable;

class WordSequences implements \IteratorAggregate
{
    private $string;
    private $forwards;
    /**
     * @param string $string
     * @param bool $forwards
     */
    public function __construct($string = '', $forwards = true)
    {
        $this->string = $string;
        $this->forwards = $forwards;
    }

    public function getIterator()
    {
        $words = get_words($this->string);

        $iterator =
            ($this->forwards)
            ? function() use($words) {
                for($i = 0; $i < count($words); $i++) {
                    yield $i;
                }
            }
            : function() use($words) {
                for($i = count($words) - 1; $i >= 0; $i--) {
                    yield $i;
                }
            };

        $slicer =
            ($this->forwards)
            ? function($i) use($words) {
                return array_slice($words, 0, $i+1);
            }
            : function($i) use($words) {
                return array_slice($words, $i);
            };

        foreach($iterator() as $i) {
            yield $slicer($i);
        }
    }


    public function getSequences()
    {
        return iterator_to_array($this);
    }
}
