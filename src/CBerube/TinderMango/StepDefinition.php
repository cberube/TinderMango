<?php

namespace CBerube\TinderMango;

class StepDefinition
{
    private static $prefixPatternList = array(
        '/^Given/i' => 'Given',
        '/^When/i' => 'When',
        '/^Then/i' => 'Then'
    );

    public $regex;
    public $type;
    public $function;
    public $description;

    public function __construct($definition, $function, $description)
    {
        $this->decomposeDefinition($definition);
        $this->function = $this->trimAndRemoveLeadingCharacter($function, '#');
        $this->description = $this->trimAndRemoveLeadingCharacter($description, '-');
    }

    private function trimAndRemoveLeadingCharacter($value, $leading)
    {
        $escapedLeading = preg_quote($leading);
        return trim(preg_replace("/$escapedLeading/", '', $value, 1));
    }

    private function decomposeDefinition($definition)
    {
        $definition = trim($definition);

        $this->type = $this->determineTypeFromDefinition($definition);

        $this->regex = trim(substr($definition, strlen($this->type) + 1));

        $regexPattern = '~/\\^.*\\$/~';
        if (preg_match($regexPattern, $this->regex) == 1) {
            $this->regex = substr(substr($this->regex, 2), 0, -2);
        }
    }

    private function determineTypeFromDefinition($definition)
    {
        foreach (static::$prefixPatternList as $pattern => $stepType) {
            if (preg_match($pattern, $definition)) {
                return $stepType;
            }
        }

        throw new \Exception("Unable to determine step type from definition: $definition");
    }
}
