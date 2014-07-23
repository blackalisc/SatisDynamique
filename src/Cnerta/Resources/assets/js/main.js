var app = angular.module('satisdynamique', ['ngResource', 'ui.bootstrap', 'xeditable']);

app.run(function(editableOptions) {
  editableOptions.theme = 'bs3';
});

app.factory('SatisDynamique', ['$resource',
    function($resource) {
        var myService = {
        allPakage: function() {
            return $resource('http://localhost/SatisDynamique/pakages');
        },
        postPakage: function() {
            return $resource('http://localhost/SatisDynamique/pakage');
        },
        allRepositories: function() {
            return $resource('http://localhost/SatisDynamique/repositories');
        },
        postRepository: function() {
            return $resource('http://localhost/SatisDynamique/repository');
        }
    };
    return myService;
    }
]);
