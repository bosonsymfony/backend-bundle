angular.module('app')
    .controller('HelpCtrl',
        ['$scope',
            function ($scope) {
                $scope.createNotify('success', 'You clicked help!!!!');
            }
        ]
    );