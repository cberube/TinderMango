function BehatScenarioController($scope, messageDispatcher)
{
    $scope.steps = [];

    $scope.$on(
        'addStep',
        function() {
            var stepData = {};

            stepData.type = messageDispatcher.content.type;
            stepData.reference = messageDispatcher.content.regex;
            stepData.content = messageDispatcher.content.regex;

            $scope.addStep(stepData);
        }
    );

    $scope.addStep = function(stepData)
    {
        $scope.steps.push(stepData);
    };

    $scope.moveStepUp = function($index)
    {
        if ($index > 0) {
            var stepToMove = $scope.steps[$index];

            $scope.steps[$index] = $scope.steps[$index - 1];
            $scope.steps[$index - 1] = stepToMove;
        }
    };

    $scope.moveStepDown = function($index)
    {
        if ($index < $scope.steps.length - 1) {
            var stepToMove = $scope.steps[$index];

            $scope.steps[$index] = $scope.steps[$index + 1];
            $scope.steps[$index + 1] = stepToMove;
        }
    };
}
