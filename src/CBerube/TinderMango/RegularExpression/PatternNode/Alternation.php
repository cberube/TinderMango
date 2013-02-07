<?php

namespace CBerube\TinderMango\RegularExpression\PatternNode;

use CBerube\TinderMango\RegularExpression\AbstractPatternNodeContainer;

class Alternation extends AbstractPatternNodeContainer implements PatternNodeInterface
{
    public function getAlternativeList()
    {
        return $this->getNodeList();
    }
}
