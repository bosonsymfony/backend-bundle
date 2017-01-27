/**
 * Created by rosi on 21/02/16.
 */
angular.module('app')
    .controller('CommandsCtrl',
        ['$scope', 'dashboard', '$sce',
            function ($scope, dashboard, $sce) {

                $scope.promise = dashboard.getCommands();

                $scope.showSelected = function (node) {
                    if (!('children' in node)) {
                        $scope.selectedCommand = $scope.commands[node.id];
                    }
                    console.log(node);
                };

                $scope.selectedCommand = {};

                $scope.promise.then(function (response) {
                    $scope.commands = response.data.commands;
                    $scope.namespaces = response.data.namespaces;
                });

                $scope.trustAsHtml = function (value) {
                    return $sce.trustAsHtml(value);
                }
            }
        ]
    );
