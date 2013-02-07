<?php

namespace CBerube\TinderMango\RegularExpression;

use CBerube\TinderMango\RegularExpression\PatternNode\PatternNodeInterface;

abstract class AbstractPatternNodeContainer
{
    private $nodeList;

    public function __construct()
    {
        $this->nodeList = array();
    }

    public function addNode(PatternNodeInterface $node)
    {
        $this->nodeList[] = $node;
    }

    public function getNodeList()
    {
        return $this->nodeList;
    }
}
