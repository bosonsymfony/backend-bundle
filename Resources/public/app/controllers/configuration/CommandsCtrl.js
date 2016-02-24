/**
 * Created by rosi on 21/02/16.
 */
angular.module('app')
    .controller('CommandsCtrl',
        ['$scope', 'dashboard',
            function ($scope, dashboard) {

                $scope.promise = dashboard.getCommands();

                $scope.promise.then(function (response) {
                    $scope.commands = response.data.commands;
                    $scope.namespaces = response.data.namespaces;
                });
            }
        ]
    );
