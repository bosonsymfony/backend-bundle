/**
 * Created by killer on 27/01/16.
 */

angular.module('app')
    .provider('module', [
            function () {

                var modules = [];

                var addModule = function (module) {
                    if (angular.isArray(module)) {
                        modules.concat(module);
                    } else {
                        modules.push(module)
                    }
                };

                var getModules = function () {
                    return modules;
                };

                var load = function (src) {
                    return {
                        deps: ['$ocLazyLoad',
                            function ($ocLazyLoad) {
                                return $ocLazyLoad.load(src);
                            }
                        ]
                    }
                };

                return {
                    $get: function () {

                    }
                }
            }
        ]
    );
