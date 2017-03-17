<?php

namespace DataValidata\FuzzyStrings;

function get_words($string) {
    $string = trim(preg_replace('!\s+!', ' ', $string));
    return explode(" ", $string);
}