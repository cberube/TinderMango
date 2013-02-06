<?php

require_once __DIR__ . '/vendor/autoload.php';

use CBerube\TinderMango\StepDefinition;

$requestId = isset($_GET['requestId']) ? intval($_GET['requestId']) : 0;
$search = isset($_GET['search']) ? $_GET['search'] : '';

chdir('/home/ec2-user/Behat');
exec('/usr/bin/php ./bin/behat -di 2>&1', $output);

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
            $stepDefinition->description
        );
        $stepDefinition->markedRegex = preg_replace(
            $searchPattern,
            $replacePattern,
            $stepDefinition->regex
        );

        $matchedStepDefinitions[] = $stepDefinition;
    }
}

$response = array();
$response['searchResults'] = $matchedStepDefinitions;
$response['requestId'] = $requestId;

header('Content-type: application/json');
echo json_encode($response);
