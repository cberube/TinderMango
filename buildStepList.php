<?php

require_once __DIR__ . '/vendor/autoload.php';

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

$stepDefinitionPath = $configuration['stepDefinitionPath'];

file_put_contents($stepDefinitionPath, json_encode($stepDefinitionList));
