angular.module('app')
    .controller('RoutesCtrl', ['$scope', 'dashboard', function ($scope, dashboard) {
        $scope.query = {
            order: 'name',
            limit: 5,
            page: 1
        };

        $scope.onReorder = function (order) {
            $scope.order = order;
        };

        $scope.promise = dashboard.getRoutes();

        $scope.promise.then(function (response) {
            $scope.routes = response.data;
        });
    }]);