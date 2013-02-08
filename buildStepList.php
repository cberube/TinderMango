<?php

require_once __DIR__ . '/vendor/autoload.php';

use CBerube\TinderMango\RegularExpression\Parser;
use CBerube\TinderMango\RegularExpression\PatternNode\Anchor\AbstractAnchor;
use CBerube\TinderMango\RegularExpression\PatternNode\Group\AbstractGroup;
use CBerube\TinderMango\RegularExpression\PatternNode\Group\Alternation;
use CBerube\TinderMango\RegularExpression\PatternNode\Group\CapturingGroup;
use CBerube\TinderMango\RegularExpression\PatternNode\Group\NonCapturingGroup;
use CBerube\TinderMango\RegularExpression\PatternNode\Literal;
use CBerube\TinderMango\RegularExpression\PatternNode\PatternNodeInterface;
use CBerube\TinderMango\StepDefinition;

$configurationSource = file_get_contents("tinderMango.json");
$configuration = json_decode($configurationSource, true);

$requestId = isset($_GET['requestId']) ? intval($_GET['requestId']) : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

chdir($configuration['behatWorkPath']);

$phpPath = $configuration['phpPath'];
$behatPath = $configuration['behatBinaryPath'];
exec("$phpPath $behatPath -di 2>&1", $output);

$stepDefinitionList = array();

while (!empty($output)) {
    $regex = array_shift($output);
    $description = array_shift($output);
    $function = array_shift($output);

    if (empty($function)) {
        $function = $description;
        $description = '';
    } else {
        //  Strip off the line of white space
        array_shift($output);
    }

    $stepDefinitionList[] = new StepDefinition($regex, $function, $description);
}

/** @var $stepDefinition \CBerube\TinderMango\StepDefinition */
foreach ($stepDefinitionList as $stepDefinition) {
    var_dump($stepDefinition);

    $parser = new Parser();
    $pattern = $parser->parse($stepDefinition->rawRegex);

    var_dump($pattern);

    //  Now we want to convert the parsed regex pattern into a simpler
    //  format that defines UI elements a user can directly interact with

    $stepUiElementList = array();
    $patternNodeList = $pattern->getNodeList();

    foreach ($patternNodeList as $node) {
        $uiNode = null;

        if ($node instanceof AbstractAnchor) {
            //  We don't care about anchors
            continue;
        } elseif ($node instanceof Literal) {
            //  Handle literals
            if (isNodeRequired($node)) {
                //  Plain old immutable text
                $uiNode = new stdClass();
                $uiNode->type = "text";
                $uiNode->value = strval($node);
            } elseif (isNodeOptional($node)) {
                //  Toggle button
                $uiNode = new stdClass();
                $uiNode->type = "toggle";
                $uiNode->value = strval($node);
            }
        } elseif ($node instanceof NonCapturingGroup) {
            //  If the group is optional and contains only a single literal node, we
            //  treat it as a toggle
            if (isNodeOptional($node)) {
                $nodeList = $node->getNodeList();

                if (count($nodeList) == 1 && ($nodeList[0] instanceof Literal)) {
                    $uiNode = new stdClass();
                    $uiNode->type = "toggle";
                    $uiNode->value = strval($nodeList[0]);
                }
            }
        } elseif ($node instanceof CapturingGroup) {
            //  Handle capturing groups
            $node->getNodeList();

            if (doesGroupContainAlternations($node)) {
                //  Groups with alternations become drop downs
                //  (We assume they will be alternations between literals, at least for now)
                $childNodeList = $node->getNodeList();

                $uiNode = new stdClass();
                $uiNode->type = "simpleChoice";
                $uiNode->value = collectLiteralsFromAlternation($childNodeList[0]);
            } else {
                //  Assume something crazy is happening here and just provide a
                //  text field for now
                $uiNode = new stdClass();
                $uiNode->type = "shortText";
                $uiNode->value = '';
            }
        }

        if (!empty($uiNode)) {
            $stepUiElementList[] = $uiNode;
        }
    }

    //  Was the last character of the regex a colon? If so, add a long text field.
    if (substr($stepDefinition->regex, -1) == ':') {
        $uiNode = new stdClass();
        $uiNode->type = "longText";
        $uiNode->value = '';
        $stepUiElementList[] = $uiNode;
    }

    $stepDefinition->uiElements = $stepUiElementList;
}

function isNodeOptional(PatternNodeInterface $node)
{
    return ($node->getMinimumOccurrences() == 0 && $node->getMaximumOccurrences() == 1);
}

function isNodeRequired(PatternNodeInterface $node)
{
    return ($node->getMinimumOccurrences() == 1 && $node->getMaximumOccurrences() == 1);
}

function doesGroupContainAlternations(AbstractGroup $group)
{
    $nodeList = $group->getNodeList();

    foreach ($nodeList as $node) {
        if ($node instanceof Alternation) {
            return true;
        }
    }

    return false;
}

function collectLiteralsFromAlternation(Alternation $alternation)
{
    $nodeList = $alternation->getNodeList();
    $valueList = array();

    foreach ($nodeList as $node) {
        if ($node instanceof Literal) {
            $valueList[] = strval($node);
        }
    }

    return $valueList;
}

$stepDefinitionPath = $configuration['stepDefinitionPath'];

file_put_contents($stepDefinitionPath, json_encode($stepDefinitionList));
