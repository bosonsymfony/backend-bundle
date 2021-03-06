/**
 * Created by killer on 7/03/16.
 */
angular.module('app')
    .directive('ngFocus',
        ['$timeout',
            function ($timeout) {
                return {
                    link: function (scope, element, attrs) {
                        scope.$watch(attrs.ngFocus, function (val) {
                            if (angular.isDefined(val) && val) {
                                $timeout(function () {
                                    element[0].focus();
                                });
                            }
                        }, true);
                    }
                };
            }
        ]
    );