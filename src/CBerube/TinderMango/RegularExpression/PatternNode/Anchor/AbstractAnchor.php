<?php

namespace CBerube\TinderMango\RegularExpression\PatternNode\Anchor;

use CBerube\TinderMango\RegularExpression\PatternNode\AbstractPatternNode;

class AbstractAnchor extends AbstractPatternNode
{
    public function __construct()
    {
        $this->setOccurrences(1, 1);
    }
}
