<?php

namespace CBerube\TinderMango\RegularExpression;

use CBerube\TinderMango\RegularExpression\PatternNode\Literal;
use CBerube\TinderMango\RegularExpression\PatternNode\Alternation;
use CBerube\TinderMango\RegularExpression\Parser\Context;

class Parser
{
    public function parse($expression)
    {
        $context = new Context($expression);

        $this->determinePatternDelimiter($context);
        $this->stripDelimiters($context);

        $this->parseBareExpression($context);

        return $context->pattern;
    }

    private function parseBareExpression(Context $context)
    {
        if ($this->doesPatternContainAlternations($context)) {
            $alternation = new Alternation();

            $subexpressionList = $this->splitPatternIntoAlternatives($context);

            foreach ($subexpressionList as $subexpression) {
                $subcontext = new Context($subexpression, $alternation);
                $this->parseBareExpression($subcontext);
            }

            $context->pattern->addNode($alternation);
        } else {
            $this->scanExpression($context);
        }
    }

    private function determinePatternDelimiter(Context $context)
    {
        $context->delimiter = $context->expression[0];
    }

    private function stripDelimiters(Context $context)
    {
        $lastDelimiterPosition = strrpos($context->expression, $context->delimiter);
        $context->expression = substr($context->expression, 1, $lastDelimiterPosition - 1);
    }

    private function doesPatternContainAlternations(Context $context)
    {
        $length = strlen($context->expression);
        $isNextTokenEscaped = false;
        $depth = 0;
        $groupStart = array('(', '[');
        $groupEnd = array(')', ']');

        for ($i = 0; $i < $length; $i++) {
            //  Since this token has been escaped, we can just skip it at this point
            if ($isNextTokenEscaped) {
                continue;
            }

            $token = $context->expression[$i];

            if (array_search($token, $groupStart) !== false) {
                $depth++;
            } elseif (array_search($token, $groupEnd) !== false) {
                $depth--;
            } elseif ($token == '\\') {
                $isNextTokenEscaped = true;
            } elseif ($token == '|' && $depth == 0) {
                return true;
            }
        }

        return false;
    }

    private function splitPatternIntoAlternatives(Context $context)
    {
        return explode('|', $context->expression);
    }

    private function scanExpression(Context $context)
    {
        $expressionStack = str_split($context->expression);

        $this->scanExpressionStack($context->pattern, $expressionStack);
    }

    private function scanExpressionStack(AbstractPatternNodeContainer $parent, $expressionStack, $terminal = null)
    {
        $expression = '';

        while (!empty($expressionStack)) {
            $token = array_shift($expressionStack);

            if ($token == $terminal) {
                //  When we hit the terminal token, we are done accumulating text
                break;
            } elseif ($token == '(') {
                //  A parenthesis indicates the start of a group
                $this->formLiteral($parent, $expression);

                $capturingGroup = new PatternNode\CapturingGroup();
                $subexpression = $this->accumulateUntil($expressionStack, ')');
                $subcontext = new Context($subexpression, $capturingGroup);
                $this->parseBareExpression($subcontext);

                $parent->addNode($capturingGroup);
            } else {
                //  Accumulate a plain old literal token and continue
                $expression .= $token;
            }
        }

        $this->formLiteral($parent, $expression);
    }

    private function accumulateUntil(&$expressionStack, $terminal)
    {
        $expression = '';

        while (!empty($expressionStack)) {
            $token = array_shift($expressionStack);

            if ($token == $terminal) {
                break;
            }

            $expression .= $token;
        }

        return $expression;
    }

    private function formLiteral(AbstractPatternNodeContainer $parent, &$expression)
    {
        if (!empty($expression)) {
            $parent->addNode(new Literal($expression));
        }

        $expression = '';
    }
}
