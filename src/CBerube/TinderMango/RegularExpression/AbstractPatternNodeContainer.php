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

    public function replaceNode($index, PatternNodeInterface $newNode)
    {
        array_splice($this->nodeList, $index, 1, array($newNode));
    }

    public function replaceLastNode(PatternNodeInterface $newNode)
    {
        $this->replaceNode(count($this->nodeList) - 1, $newNode);
    }
}
