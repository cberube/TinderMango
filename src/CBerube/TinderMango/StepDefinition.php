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
        $this->function = $function;
        $this->description = $description;
    }

    private function decomposeDefinition($definition)
    {
        $definition = trim($definition);

        $this->type = $this->determineTypeFromDefinition($definition);

        $this->regex = trim(substr($definition, strlen($this->type) + 1));
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
