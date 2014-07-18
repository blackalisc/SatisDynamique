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
        }
    };
    return myService;
    }
]);
