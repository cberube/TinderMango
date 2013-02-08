<?php

namespace CBerube\TinderMango\RegularExpression\PatternNode\Group;

class Alternation extends AbstractGroup
{
    public function getAlternativeList()
    {
        return $this->getNodeList();
    }
}
