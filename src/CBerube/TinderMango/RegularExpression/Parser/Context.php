<?php

namespace CBerube\TinderMango\RegularExpression\Parser;

use CBerube\TinderMango\RegularExpression\AbstractPatternNodeContainer;
use CBerube\TinderMango\RegularExpression\Pattern;

class Context
{
    private $originalExpression;

    /** @var \CBerube\TinderMango\RegularExpression\AbstractPatternNodeContainer */
    public $pattern;
    public $expression;
    public $delimiter;

    public function __construct($expression, AbstractPatternNodeContainer $pattern = null)
    {
        $this->originalExpression = $expression;
        $this->expression = $expression;

        $this->pattern = is_null($pattern) ? new Pattern() : $pattern;
    }
}
