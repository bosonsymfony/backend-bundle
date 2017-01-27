angular.module('app')
    .controller('DatabaseCtrl', ['$scope', 'dashboard', function ($scope, dashboard) {

        $scope.query = {
            order: 'class',
            limit: 5,
            page: 1
        };

        this.openMenu = function ($mdOpenMenu, ev) {
            //originatorEv = ev;
            $mdOpenMenu(ev);
        };

        $scope.onReorder = function (order) {
            $scope.order = order;
        };

        $scope.promise = dashboard.getDatabase();

        $scope.promise.then(
            function (response) {
                $scope.params = response.data.params;
                $scope.entities = response.data.entities;
            }
        )
    }]);