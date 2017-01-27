/**
 * Created by killer on 19/01/16.
 */

angular.module('app')
    .run(
        ['$rootScope', '$state', '$stateParams', 'cfpLoadingBar',
            function ($rootScope, $state, $stateParams, cfpLoadingBar) {
                $rootScope.$state = $state;
                $rootScope.$stateParams = $stateParams;
                $rootScope.$urlAssets = urlAsset;
                $rootScope.$appName = 'Boson';

                $rootScope.$on('$stateChangeStart',
                    function (event, toState, toParams, fromState, fromParams) {
                        cfpLoadingBar.start();
                    }
                );

                $rootScope.$on('$stateChangeSuccess',
                    function (event, toState, toParams, fromState, fromParams) {
                        cfpLoadingBar.complete();
                    }
                );
            }
        ]
    )
    .config(
        ['$stateProvider', '$urlRouterProvider',
            function ($stateProvider, $urlRouterProvider) {

                $urlRouterProvider
                    .otherwise('app/dashboard');

                //routes here
                $stateProvider
                    .state('app', {
                        abstract: true,
                        url: '/app',
                        views: {
                            '': {
                                templateUrl: urlAsset + 'bundles/backend/app/views/layout.html'
                            },
                            'aside': {
                                templateUrl: urlAsset + 'bundles/backend/app/views/aside.html'
                            },
                            'content': {
                                templateUrl: urlAsset + 'bundles/backend/app/views/content.html'
                            }
                        }
                    })
                    .state('app.dashboard', {
                        url: '/dashboard',
                        templateUrl: urlAsset + 'bundles/backend/app/views/pages/dashboard.html',
                        data: {
                            title: 'Dashboard',
                            //folded: true
                        }
                    });

                function load(src) {
                    return {
                        deps: ['$ocLazyLoad', function ($ocLazyLoad) {
                            // you can lazy load files for an existing module
                            return $ocLazyLoad.load(src);
                        }]
                    }
                }
            }
        ]
    );
