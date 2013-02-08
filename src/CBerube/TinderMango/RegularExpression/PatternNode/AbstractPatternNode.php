<?php

namespace CBerube\TinderMango\RegularExpression\PatternNode;

class AbstractPatternNode implements PatternNodeInterface
{
    private $minimumOccurrences = 1;
    private $maximumOccurrences = 1;

    public function setMinimumOccurrences($min)
    {
        $this->minimumOccurrences = $min;
    }

    public function getMinimumOccurrences()
    {
        return $this->minimumOccurrences;
    }

    public function setMaximumOccurrences($max)
    {
        $this->maximumOccurrences = $max;
    }

    public function getMaximumOccurrences()
    {
        return $this->maximumOccurrences;
    }

    public function setOccurrences($min, $max)
    {
        $this->setMinimumOccurrences($min);
        $this->setMaximumOccurrences($max);
    }
}
