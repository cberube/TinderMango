<?php

namespace CBerube\TinderMango\RegularExpression\PatternNode\Group;

use CBerube\TinderMango\RegularExpression\AbstractPatternNodeContainer;
use CBerube\TinderMango\RegularExpression\PatternNode\PatternNodeInterface;

class AbstractGroup extends AbstractPatternNodeContainer implements PatternNodeInterface
{
    private $minimumOccurrences = 1;
    private $maximumOccurrences = 1;

    public function __construct($minOccurrences = 1, $maxOccurrences = 1)
    {
        $this->setOccurrences($minOccurrences, $maxOccurrences);
    }

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

    public function setOccurrences($min, $max = null)
    {
        $this->setMinimumOccurrences($min);
        $this->setMaximumOccurrences(is_null($max) ? $min : $max);
    }
}
