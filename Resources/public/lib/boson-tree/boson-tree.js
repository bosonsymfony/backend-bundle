/**
 * Created by killer on 29/02/16.
 */

angular.module('boson-tree', [])
    .directive('treeCollection', [
            function () {
                return {
                    restrict: "E",
                    replace: true,
                    scope: {
                        collection: '=',
                        iconLeaf: '='
                    },
                    template: "<ul><node ng-repeat='node in collection' node='node'></node></ul>"
                }
            }
        ]
    )
    .directive('node', ['$compile',
            function ($compile) {
                return {
                    restrict: "E",
                    replace: true,
                    scope: {
                        node: '='
                    },
                    template: "<li>{{ node.label }}</li>",
                    link: function (scope, element, attrs) {
                        if (angular.isArray(scope.node.children)) {
                            element.append("<tree-collection collection='node.children'></tree-collection>");
                            $compile(element.contents())(scope)
                        }else {

                        }
                    }
                }
            }
        ]
    );
