/**
 * Created by rosi on 24/01/16.
 */

angular.module('app')
    .factory('dashboard',
        ['$http',
            function ($http) {

                function getBundles() {
                    return $http.get(Routing.generate('backend_dashboard_bundles', {}, true));
                }

                function getRoutes() {
                    return $http.get(Routing.generate('backend_dashboard_routing', {}, true));
                }

                function getDatabase() {
                    return $http.get(Routing.generate('backend_dashboard_database', {}, true));
                }

                function getCommands() {
                    return $http.get(Routing.generate('backend_dashboard_commands', {}, true));
                }

                return {
                    getBundles: getBundles,
                    getRoutes: getRoutes,
                    getDatabase: getDatabase,
                    getCommands: getCommands
                }
            }
        ]
    );
