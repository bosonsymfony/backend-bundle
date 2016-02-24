//console.log('Cargue este archivo');

angular.module('app')
    .controller('AppCtrl',
        ['$scope', 'cfpLoadingBar', '$mdSidenav', '$timeout',
            function ($scope, cfpLoadingBar, $mdSidenav, $timeout) {

                console.log('Entre aquiii');

                $scope.start = function () {
                    console.log('start');
                    cfpLoadingBar.start();
                };

                $scope.complete = function () {
                    console.log('complete');
                    cfpLoadingBar.complete();
                };

                $scope.openMenu = function () {
                    $timeout(function () {
                        $mdSidenav('app-aside').open();
                    });
                }
            }
        ]
    );