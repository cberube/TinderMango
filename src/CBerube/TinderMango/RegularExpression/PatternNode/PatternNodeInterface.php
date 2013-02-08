<?php

namespace CBerube\TinderMango\RegularExpression\PatternNode;

interface PatternNodeInterface
{
    public function getMinimumOccurrences();
    public function setMinimumOccurrences($min);

    public function getMaximumOccurrences();
    public function setMaximumOccurrences($max);

    public function setOccurrences($min, $max);
}
