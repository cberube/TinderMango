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
            stepData.uiElements = JSON.parse(JSON.stringify(messageDispatcher.content.uiElements));
            stepData.currentIndex = $scope.steps.length;

            angular.forEach(
                stepData.uiElements,
                function (ui) {
                    ui.currentValue = angular.isArray(ui.value) ? ui.value[0] : ui.value;
                }
            );

            $scope.addStep(stepData);
        }
    );

    $scope.addStep = function(stepData)
    {
        $scope.steps.push(stepData);
        setTimeout(
            function () {
                jQuery('.tooltipSource').tooltip();
                jQuery('.longText').autosize();
            },
            100
        );

    };

    $scope.moveStepUp = function($index)
    {
        if ($index > 0) {
            var stepToMove = $scope.steps[$index];

            $scope.steps[$index] = $scope.steps[$index - 1];
            $scope.steps[$index - 1] = stepToMove;

            stepToMove.currentIndex--;
            $scope.steps[$index].currentIndex++;
        }
    };

    $scope.moveStepDown = function($index)
    {
        if ($index < $scope.steps.length - 1) {
            var stepToMove = $scope.steps[$index];

            $scope.steps[$index] = $scope.steps[$index + 1];
            $scope.steps[$index + 1] = stepToMove;

            stepToMove.currentIndex++;
            $scope.steps[$index].currentIndex--;
        }
    };

    $scope.deleteStep = function($index)
    {
        $scope.steps.splice($index, 1);
    };

    $scope.getRawText = function()
    {
        var text = '';
        var i;

        for (i = 0; i < $scope.steps.length; i++) {
            text +=
                $scope.steps[i].type.leftPad(5) + ' ' +
                    $scope.accumulateUiContent($scope.steps[i]) + "\r\n";
        }

        return text;
    };

    $scope.accumulateUiContent = function(step)
    {
        var text = '';
        var lines = null;

        angular.forEach(
            step.uiElements,
            function (ui) {
                if (ui.type == 'longText') {
                    text += "\r\n";
                    lines = ui.currentValue.split(/\n/);

                    angular.forEach(
                        lines,
                        function (line) {
                            text += '        ' + line + "\r\n";
                        }
                    );
                } else {
                    text += ui.currentValue;
                }
            }
        );

        return text;
    };

    $scope.toggleUiValue = function($stepIndex, $uiIndex)
    {
        var ui = $scope.steps[$stepIndex].uiElements[$uiIndex];

        ui.currentValue = (ui.currentValue == '' ? ui.value : '');
    }
}
