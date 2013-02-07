var BehatSearchApp = angular.module('BehatSearchApp', ['ngSanitize']);

String.prototype.repeat = function(num)
{
    return new Array(num + 1).join(this);
};

BehatSearchApp.factory(
    "messageDispatcher",
    function($rootScope)
    {
        var dispatcher = {};

        dispatcher.content = null;

        dispatcher.dispatchAddStep = function(content)
        {
            this.content = content;
            $rootScope.$broadcast('addStep')
        };

        return dispatcher;
    }
);

BehatSearchApp.filter(
    "padLeft",
    function() {
        return function(text, length) {
            if (text.length < length) {
                text = ' '.repeat((length - text.length)) + text;
            }

            return text;
        }
    }
);

function BehatSearchController($scope, messageDispatcher)
{
    $scope.results = [];
    $scope.search = '';

    $scope.searchChangeId = 0;
    $scope.lastResponseId = 0;
    $scope.lastSearchChangeTimeout = false;

    $scope.doSearch = function()
    {
        var $currentScope = $scope;
        var currentChangeId = ++this.searchChangeId;

        if ($scope.lastSearchChangeTimeout != false) {
            clearTimeout($scope.lastSearchChangeTimeout);
            $scope.lastSearchChangeTimeout = false;
        }

        $scope.lastSearchChangeTimeout = setTimeout(
            function () {
                $currentScope.requestSearchResults(currentChangeId);
            },
            200
        );
    };

    $scope.requestSearchResults = function(changeId)
    {
        var $currentScope = $scope;

        //  The search term has been modified since the timeout was requested,
        //  so we are out-of-date and should do nothing
        if (changeId < this.searchChangeId) {
            return;
        }

        jQuery.ajax(
            'find.php',
            {
                data:
                {
                    requestId: this.searchChangeId,
                    search: this.search
                },
                success: function(response)
                {
                    //  If we are out of date, do nothing
                    if (response.requestId < $currentScope.searchChangeId) {
                        return;
                    }

                    $currentScope.results = response.searchResults;
                    $currentScope.lastResponseID = response.requestId;

                    //  Update the view
                    $currentScope.$apply();
                }
            }
        )
    };

    $scope.addStepToScenario = function(step)
    {
        messageDispatcher.dispatchAddStep(step);
    }
}
