<?php

require_once __DIR__ . '/../vendor/autoload.php';

use CBerube\TinderMango\StepDefinition;

$configurationSource = file_get_contents(__DIR__ . "/../tinderMango.json");
$configuration = json_decode($configurationSource, true);

$stepDefinitionListSource = file_get_contents($configuration['stepDefinitionPath']);
$stepDefinitionList = json_decode($stepDefinitionListSource);

$requestId = isset($_GET['requestId']) ? intval($_GET['requestId']) : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$escapedSearch = str_replace(
    array('~', '(', ')'),
    array('\\~', '\\(', '\\)'),
    $search
);
$searchPattern = "~($escapedSearch)~i";
$replacePattern = '<mark>\\1</mark>';

$matchedStepDefinitions = array();

/** @var $stepDefinition \CBerube\TinderMango\StepDefinition */
foreach ($stepDefinitionList as $stepDefinition) {
    if (preg_match($searchPattern, $stepDefinition->description) === 1
        || preg_match($searchPattern, $stepDefinition->regex) === 1
    ) {
        $stepDefinition->markedDescription = preg_replace(
            $searchPattern,
            $replacePattern,
            htmlentities($stepDefinition->description)
        );
        $stepDefinition->markedRegex = preg_replace(
            $searchPattern,
            $replacePattern,
            htmlentities($stepDefinition->regex)
        );

        $matchedStepDefinitions[] = $stepDefinition;
    }
}

$response = array();
$response['searchResults'] = $matchedStepDefinitions;
$response['requestId'] = $requestId;

header('Content-type: application/json');
echo json_encode($response);
