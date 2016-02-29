/**
 * Created by rosi on 21/02/16.
 */
angular.module('app')
    .controller('CommandsCtrl',
        ['$scope', 'dashboard',
            function ($scope, dashboard) {

                $scope.promise = dashboard.getCommands();

                $scope.data = [{
                    label: 'Languages',
                    children: [
                        {
                            label: 'Jade'
                        },
                        {
                            label: 'Less'
                        },
                        {
                            label: 'Coffeescript',
                            children: [
                                {
                                    label: 'Jade'
                                },
                                {
                                    label: 'Less'
                                },
                                {
                                    label: 'Coffeescript',
                                    onSelect: function (branch) {
                                        console.log(branch);
                                    }
                                }
                            ]
                        }
                    ]
                }];

                $scope.promise.then(function (response) {
                    $scope.commands = response.data.commands;
                    $scope.namespaces = response.data.namespaces;
                });
            }
        ]
    );
