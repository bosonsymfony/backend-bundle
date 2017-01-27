angular.module('app')
    .controller('BundlesCtrl',
        ['$scope', 'dashboard',
            function ($scope, dashboard) {

                $scope.query = {
                    order: 'name',
                    limit: 5,
                    page: 1
                };

                $scope.onReorder = function (order) {
                    $scope.order = order;
                };

                $scope.promise = dashboard.getBundles();

                $scope.promise.then(function (response) {
                    $scope.bundles = response.data;
                });
            }
        ]
    );