<?php

namespace CBerube\TinderMango\RegularExpression\PatternNode;

class Literal extends AbstractPatternNode
{
    private $value;

    public function __construct($value, $minOccurrences = 1, $maxOccurrences = 1)
    {
        $this->value = $value;
        $this->setOccurrences($minOccurrences, $maxOccurrences);
    }

    public function __toString()
    {
        return strval($this->value);
    }
}
