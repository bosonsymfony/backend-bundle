/**
 * Created by killer on 19/01/16.
 */

angular.module('app')
    .constant('MODULES', [
            {
                name: 'dashboard',
                files: [
                    urlAsset + 'bundles/backend/app/controllers/AppCtrl.js'
                ]
            }
        ]
    )
    .config(
        ['$ocLazyLoadProvider', 'MODULES', 'cfpLoadingBarProvider',
            function ($ocLazyLoadProvider, MODULES, cfpLoadingBarProvider) {
                cfpLoadingBarProvider.includeSpinner = false;

                $ocLazyLoadProvider.config({
                    debug: false,
                    events: false,
                    modules: MODULES
                })
            }
        ]
    );