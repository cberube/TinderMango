<?php

namespace CBerube\TinderMango\RegularExpression\PatternNode;

class Literal implements PatternNodeInterface
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return strval($this->value);
    }
}
